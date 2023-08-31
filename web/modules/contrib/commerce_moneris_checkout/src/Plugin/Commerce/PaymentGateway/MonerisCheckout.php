<?php

namespace Drupal\commerce_moneris_checkout\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_moneris_checkout\Event\MonerisCheckoutEvents;
use Drupal\commerce_moneris_checkout\Event\MonerisCheckoutTransactionCancelEvent;
use Drupal\commerce_moneris_checkout\MonerisCheckoutResponseCodes;
use Drupal\commerce_moneris_checkout\MonerisCheckoutResponseStates;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Exception\HardDeclineException;
use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides the Moneris Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "moneris_checkout",
 *   label = "Moneris Checkout",
 *   display_label = "Moneris Checkout",
 *   forms = {
 *     "offsite-payment" = "Drupal\commerce_moneris_checkout\PluginForm\MonerisCheckoutForm",
 *   },
 *   modes = {
 *     "qa" = @Translation("Testing"),
 *     "prod" = @Translation("Production"),
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "discover", "jcb", "mastercard", "visa",
 *   },
 *   requires_billing_information = FALSE,
 * )
 */
class MonerisCheckout extends OffsitePaymentGatewayBase implements MonerisCheckoutInterface {

  /**
   * Moneris Checkout QA mode.
   */
  const MONERIS_CHECKOUT_QA_ENV = 'qa';

  /**
   * Moneris Checkout Prod mode.
   */
  const MONERIS_CHECKOUT_PROD_ENV = 'prod';

  /**
   * Moneris Checkout QA Gateway.
   */
  const MONERIS_CHECKOUT_QA_GATEWAY = 'https://gatewayt.moneris.com';

  /**
   * Moneris Checkout PROD Gateway.
   */
  const MONERIS_CHECKOUT_PROD_GATEWAY = 'https://gateway.moneris.com';

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The 20 character descriptor sent with transactions.
   *
   * @var string
   */
  protected $dynamicDescriptor;

  /**
   * The logging channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannel
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);

    $instance->eventDispatcher = $container->get('event_dispatcher');
    $instance->httpClient = $container->get('http_client');
    $instance->logger = $container->get('logger.channel.commerce_moneris_checkout');
    $config_factory = $container->get('config.factory');
    $instance->dynamicDescriptor = substr($config_factory->get('system.site')->get('name'), 0, 20);

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'store_id' => '',
      'api_token' => '',
      'checkout_id' => '',
      'country_code' => 'CA',
      'order_number_strategy' => 'order_id',
      'api_logging' => [
        'request' => 'request',
        'response' => 'response',
      ],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['mode']['#title'] = $this->t('Environment');

    $form['store_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Store ID'),
      '#default_value' => $this->configuration['store_id'] ?? '',
      '#required' => TRUE,
    ];

    $form['api_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Token'),
      '#default_value' => $this->configuration['api_token'] ?? '',
      '#required' => TRUE,
    ];

    $form['checkout_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Checkout ID'),
      '#default_value' => $this->configuration['checkout_id'] ?? '',
      '#required' => TRUE,
    ];

    $form['country_code'] = [
      '#type' => 'select',
      '#options' => [
        'CA' => $this->t('Canada'),
        'US' => $this->t('US'),
      ],
      '#title' => $this->t('Country'),
      '#default_value' => $this->configuration['country_code'] ?? 'CA',
    ];

    $form['order_number_strategy'] = [
      '#type' => 'radios',
      '#title' => $this->t('Moneris Checkout order number strategy'),
      '#options' => [
        'order_id' => $this->t('Use the order ID'),
        'order_id_timestamp' => $this->t('Use the order ID with timestamp appended'),
        'order_number' => $this->t('Generate the order number early and use it instead of the ID'),
      ],
      '#description' => $this->t('Note: generating order numbers will likely result in out of order or missing order numbers based on if / when the orders are placed.'),
      '#default_value' => $this->configuration['order_number_strategy'] ?? 'order_id',
    ];

    $form['api_logging'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Log the following messages for debugging'),
      '#options' => [
        'request' => $this->t('API request messages'),
        'response' => $this->t('API response messages'),
      ],
      '#default_value' => $this->configuration['api_logging'] ?? [],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['store_id'] = $values['store_id'];
      $this->configuration['api_token'] = $values['api_token'];
      $this->configuration['checkout_id'] = $values['checkout_id'];
      $this->configuration['country_code'] = $values['country_code'];
      $this->configuration['order_number_strategy'] = $values['order_number_strategy'];
      $this->configuration['api_logging'] = $values['api_logging'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onReturn(OrderInterface $order, Request $request) {
    $query = $request->query->all();

    // Log the response if specified.
    if (!empty($this->configuration['api_logging']['response'])) {
      $this->logger->debug('MonerisCheckout API onReturn response: @param', [
        '@param' => new FormattableMarkup('<pre>' . print_r($query, 1) . '</pre>', []),
      ]);
    }

    $order_mco_data = $order->getData('moneris_checkout', []);
    // Remove the payment information from the order data array
    // so that a new ticket is created in case of an error.
    $order->unsetData('moneris_checkout');

    if (empty($query['ticket']) || empty($query['response_code']) || empty($query['response_state'])) {
      throw new PaymentGatewayException('The required response parameters are missing for this Moneris Checkout transaction.');
    }

    // Validate/check response code.
    if ($query['response_code'] !== MonerisCheckoutResponseCodes::MONERIS_CHECKOUT_SUCCESS) {
      throw new PaymentGatewayException(sprintf('Payment has failed or errored out by the gateway (%s).', $query['response_code']), $query['response_code']);
    }

    // Get MCO receipt to verify ticket/request.
    $payment_receipt = $this->getReceipt($query['ticket']);
    if (empty($order_mco_data) || $order_mco_data['order_no'] !== $payment_receipt['order_no']) {
      throw new PaymentGatewayException('The order number is missing or invalid.');
    }

    // @todo Check dynamic descriptor?
    if (!empty($this->dynamicDescriptor)) {

    }

    // Set the order number if one was generated.
    if ($this->getOrderNumberStrategy() === 'order_number') {
      $order->setOrderNumber($order_mco_data['order_no']);
    }
    $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
    $payment = $payment_storage->create([
      'state' => $query['response_state'] == MonerisCheckoutResponseStates::MONERIS_CHECKOUT_COMPLETE ? 'completed' : 'authorization',
      'amount' => $order->getBalance(),
      'payment_gateway' => $this->parentEntity->id(),
      'order_id' => $order->id(),
      'remote_id' => $payment_receipt['reference_no'],
      'remote_state' => $query['response_state'],
    ]);
    $payment->save();
  }

  /**
   * {@inheritdoc}
   */
  public function onCancel(OrderInterface $order, Request $request) {
    $query = $request->query->all();

    // Log the response if specified.
    if (!empty($this->configuration['api_logging']['response'])) {
      $this->logger->debug('MonerisCheckout API onCancel response: @param', [
        '@param' => new FormattableMarkup('<pre>' . print_r($query, 1) . '</pre>', []),
      ]);
    }

    // Remove the payment information from the order data array.
    $order->unsetData('moneris_checkout');

    // Cancel response state.
    if ($query['response_state'] == MonerisCheckoutResponseStates::MONERIS_CHECKOUT_CANCEL) {
      $this->messenger()->addMessage($this->t('You have canceled checkout at @gateway but may resume the checkout process here when you are ready.', [
        '@gateway' => $this->getDisplayLabel(),
      ]));

      // Trigger transaction cancel event.
      $event = new MonerisCheckoutTransactionCancelEvent($order);
      $this->eventDispatcher->dispatch($event, MonerisCheckoutEvents::MCO_TRANSACTION_CANCEL);
    }

    // Error response state.
    if ($query['response_state'] == MonerisCheckoutResponseStates::MONERIS_CHECKOUT_ERROR) {
      if (!empty($query['response_code'])) {
        $this->messenger()
          ->addError($this->t('Checkout error occurred at @gateway with response code: @response_code. Please try again with the checkout process.', [
            '@gateway' => $this->getDisplayLabel(),
            '@response_code' => $query['response_code'],
          ]));
      }
      else {
        $this->messenger()
          ->addError($this->t('Checkout error occurred at @gateway. Please try again with the checkout process.', [
            '@gateway' => $this->getDisplayLabel(),
          ]));
      }
    }
  }

  /**
   * Get the receipt.
   *
   * @param string $ticket
   *   The MCO ticket id.
   *
   * @return array|null
   *   Returns the receipt request for a specific ticket.
   */
  public function getReceipt(string $ticket): ?array {
    $body = [
      'store_id' => $this->getStoreId(),
      'api_token' => $this->getApiToken(),
      'checkout_id' => $this->getCheckoutId(),
      'ticket' => $ticket,
      'environment' => $this->getMode(),
      'action' => 'receipt',
    ];

    try {
      $response = $this->apiRequest($body);

      if ($response['response']['success'] !== 'true') {
        throw new PaymentGatewayException(sprintf('Moneris payment receipt request failed. Message: %s.', $response['response']['error']));
      }

      if ($response['response']['receipt']['result'] !== 'a') {
        throw new HardDeclineException('Moneris payment transaction declined.');
      }

      $receipt = $response['response']['receipt']['cc'];
    }
    catch (RequestException $e) {
      throw new PaymentGatewayException(sprintf('Moneris payment receipt request failed. Message: %s.', $e->getMessage()), $e->getCode(), $e);
    }

    return $receipt;
  }

  /**
   * {@inheritdoc}
   */
  public function getStoreId() {
    return $this->configuration['store_id'] ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function getApiToken() {
    return $this->configuration['api_token'] ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function getCheckoutId() {
    return $this->configuration['checkout_id'] ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function getCountryCode() {
    return $this->configuration['country_code'] ?? 'CA';
  }

  /**
   * {@inheritdoc}
   */
  public function getOrderNumberStrategy() {
    return $this->configuration['order_number_strategy'] ?? 'order_id';
  }

  /**
   * {@inheritdoc}
   */
  public function getCheckoutGateway() {
    if ($this->getMode() === self::MONERIS_CHECKOUT_QA_ENV) {
      return self::MONERIS_CHECKOUT_QA_GATEWAY;
    }
    else {
      return self::MONERIS_CHECKOUT_PROD_GATEWAY;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function apiRequest(array $body): ?array {
    $request_url = $this->getCheckoutGateway() . '/chktv2/request/request.php';

    // Log the request if specified.
    if (!empty($this->configuration['api_logging']['request'])) {
      $this->logger->debug('MonerisCheckout API request to @url: @param', [
        '@url' => $request_url,
        '@param' => new FormattableMarkup('<pre>' . print_r($body, 1) . '</pre>', []),
      ]);
    }

    // Submit the request to MonerisCheckout.
    $response = $this->httpClient->post($request_url, [
      'body' => Json::encode($body),
    ]);
    $result = Json::decode($response->getBody()->getContents());

    // Log the response if specified.
    if (!empty($this->configuration['api_logging']['response'])) {
      $this->logger->debug('MonerisCheckout API server response: @param', [
        '@param' => new FormattableMarkup('<pre>' . print_r($result, 1) . '</pre>', []),
      ]);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getOrderNumber(OrderInterface $order) {
    $order_number = '';

    switch ($this->getOrderNumberStrategy()) {
      case 'order_id':
        $order_number = $order->id();
        break;

      case 'order_id_timestamp':
        $order_number = $order->id() . '-' . $this->time->getRequestTime();
        break;

      case 'order_number':
        if (!$order->getOrderNumber()) {
          $order_type_storage = $this->entityTypeManager->getStorage('commerce_order_type');
          /** @var \Drupal\commerce_order\Entity\OrderTypeInterface $order_type */
          $order_type = $order_type_storage->load($order->bundle());
          /** @var \Drupal\commerce_number_pattern\Entity\NumberPatternInterface $number_pattern */
          $number_pattern = $order_type->getNumberPattern();
          if ($number_pattern) {
            $order_number = $number_pattern->getPlugin()->generate($order);
          }
          else {
            $order_number = $order->id();
          }
        }
        else {
          $order_number = $order->getOrderNumber();
        }
        break;
    }

    return $order_number;
  }

}
