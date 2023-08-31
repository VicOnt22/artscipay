<?php

namespace Drupal\commerce_moneris_checkout\PluginForm;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannel;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\commerce_moneris_checkout\Plugin\Commerce\PaymentGateway\MonerisCheckoutInterface;
use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Moneris Checkout Form.
 */
class MonerisCheckoutForm extends BasePaymentOffsiteForm implements ContainerInjectionInterface {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

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
   * Constructs a new MonerisCheckoutForm object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   * @param \Drupal\Core\Logger\LoggerChannel $logger_channel
   *   The logger channel.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(
    ClientInterface $http_client,
    LanguageManagerInterface $language_manager,
    LoggerChannel $logger_channel,
    ConfigFactoryInterface $config_factory
  ) {
    $this->httpClient = $http_client;
    $this->languageManager = $language_manager;
    $this->logger = $logger_channel;
    $this->dynamicDescriptor = substr($config_factory->get('system.site')->get('name'), 0, 20);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('language_manager'),
      $container->get('logger.channel.commerce_moneris_checkout'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;

    /** @var \Drupal\commerce_moneris_checkout\Plugin\Commerce\PaymentGateway\MonerisCheckoutInterface $payment_gateway_plugin */
    $payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();

    // Setup Moneris Checkout (iframe) form.
    $form['#attributes']['class'][] = 'commerce-moneris-checkout-form';

    // Attach base js.
    $form['#attached']['library'][] = 'commerce_moneris_checkout/moneris_checkout';

    // Check configured environment.
    $environment = $payment_gateway_plugin->getMode();
    if (!empty($environment)) {
      $form['#attached']['library'][] = 'commerce_moneris_checkout/moneris_checkout_' . $environment;
    }

    // Pass settings through to JS.
    $form['#attached']['drupalSettings']['commerceMonerisCheckout'] = [
      // @todo Setup debug mode/configuration field.
      'debug' => FALSE,
      'ticket' => $this->getTicket($payment, $payment_gateway_plugin),
      'mode' => $environment,
      'div' => 'monerisCheckout',
      'return_url' => $form['#return_url'],
      'cancel_url' => $form['#cancel_url'],
    ];

    // Moneris checkout placeholder.
    $form['moneris_checkout'] = [
      '#theme' => 'commerce_moneris_checkout',
    ];

    return $form;
  }

  /**
   * Get allowable languages.
   *
   * @return array
   *   Returns an array of allowed languages.
   */
  protected function allowedLanguages(): array {
    return ['en', 'fr'];
  }

  /**
   * Get the ticket id. Performs the checkout preload.
   *
   * @param \Drupal\commerce_payment\Entity\PaymentInterface $payment
   *   The payment.
   * @param \Drupal\commerce_moneris_checkout\Plugin\Commerce\PaymentGateway\MonerisCheckoutInterface $payment_gateway_plugin
   *   Moneris Checkout payment gateway plugin.
   *
   * @return string|null
   *   Returns the ticket id to render checkout form.
   */
  protected function getTicket(PaymentInterface $payment, MonerisCheckoutInterface $payment_gateway_plugin): ?string {
    $order = $payment->getOrder();
    $order_mco_data = $order->getData('moneris_checkout', []);

    // If a ticket has already been created, we don't need to create it again.
    if (!empty($order_mco_data)) {
      return $order_mco_data['ticket'];
    }

    $moneris_order_id = $payment_gateway_plugin->getOrderNumber($order);

    $body = [
      'store_id' => $payment_gateway_plugin->getStoreId(),
      'api_token' => $payment_gateway_plugin->getApiToken(),
      'checkout_id' => $payment_gateway_plugin->getCheckoutId(),
      'txn_total' => number_format($payment->getAmount()->getNumber(), 2, '.', ''),
      'environment' => $payment_gateway_plugin->getMode(),
      'action' => 'preload',
      'order_no' => $moneris_order_id,
      'cust_id' => $order->getCustomerId(),
      'dynamic_descriptor' => $this->dynamicDescriptor,
      'cart' => $this->getShoppingCartItems($payment),
      'contact_details' => $this->getContactDetails($order),
      'shipping_details' => $this->getShippingDetails($order),
      'billing_details' => $this->getBillingDetails($order),
    ];

    // Set language.
    if (in_array($this->languageManager->getCurrentLanguage()->getId(), $this->allowedLanguages())) {
      $body['language'] = $this->languageManager->getCurrentLanguage()->getId();
    }

    try {
      $response = $payment_gateway_plugin->apiRequest($body);

      if ($response['response']['success'] !== 'true') {
        throw new PaymentGatewayException(sprintf('Moneris checkout preload request failed. Error: %s', '<pre>' . print_r($response['response']['error'], 1) . '</pre>'));
      }

      $ticket = $response['response']['ticket'];
    }
    catch (RequestException $e) {
      throw new PaymentGatewayException(sprintf('Moneris checkout preload request failed. Message: %s.', $e->getMessage()), $e->getCode(), $e);
    }

    $order->setData('moneris_checkout', [
      'ticket' => $ticket,
      'order_no' => $moneris_order_id,
    ]);
    $order->save();

    return $ticket;
  }

  /**
   * Build and get shopping cart items from Order object.
   *
   * @param \Drupal\commerce_payment\Entity\PaymentInterface $payment
   *   The payment.
   *
   * @return array
   *   Returns the items in the shopping cart.
   */
  protected function getShoppingCartItems(PaymentInterface $payment): array {
    $items = [
      'subtotal' => number_format($payment->getAmount()->getNumber(), 2, '.', ''),
      'tax' => $this->getTaxInformation(),
    ];

    $order = $payment->getOrder();
    foreach ($order->getItems() as $order_item) {
      /** @var \Drupal\commerce_product\Entity\ProductVariation */
      $product_variation = $order_item->getPurchasedEntity();
      $product = $product_variation->getProduct();

      $items['items'][] = [
        'url' => $product->toUrl('canonical', ['absolute' => TRUE])->toString(),
        'description' => $order_item->getTitle(),
        'product_code' => $product_variation->getSku(),
        'unit_cost' => number_format($order_item->getUnitPrice()->getNumber(), 2, '.', ''),
        'quantity' => (int) $order_item->getQuantity(),
      ];
    }

    return $items;
  }

  /**
   * Get tax information.
   *
   * @return array
   *   A render array.
   */
  protected function getTaxInformation(): array {
    // @todo Figure out how to get tax information.
    return [
      'amount' => '',
      'description' => '',
      'rate' => '',
    ];
  }

  /**
   * Get the contact details.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order.
   *
   * @return array
   *   Returns an array of contact details.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function getContactDetails(OrderInterface $order): array {
    $billing_profile = $order->getBillingProfile();

    /** @var \CommerceGuys\Addressing\Address $billing_address */
    $billing_address = $billing_profile->get('address')->first();

    // Get phone information.
    // @todo Make configurable on payment gateway.
    $phone_field = 'phone';
    $phone = '';
    if ($billing_profile->hasField($phone_field)) {
      $phone = $billing_profile->get($phone_field)->getValue();
    }

    return [
      'first_name' => $billing_address->getGivenName(),
      'last_name' => $billing_address->getFamilyName(),
      'email' => $order->getEmail(),
      'phone' => $phone,
    ];
  }

  /**
   * Get the shipping details.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order.
   *
   * @return array
   *   Returns an array of shipping details.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function getShippingDetails(OrderInterface $order): array {
    /** @var \CommerceGuys\Addressing\Address $billing_address */
    $billing_address = $order->getBillingProfile()->get('address')->first();

    return [
      'address_1' => $billing_address->getAddressLine1(),
      'address_2' => $billing_address->getAddressLine2(),
      'city' => $billing_address->getLocality(),
      'province' => $billing_address->getAdministrativeArea(),
      'country' => $billing_address->getCountryCode(),
      'postal_code' => $billing_address->getPostalCode(),
    ];
  }

  /**
   * Get the billing details.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order.
   *
   * @return array
   *   Returns an array of billing details.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function getBillingDetails(OrderInterface $order): array {
    /** @var \CommerceGuys\Addressing\Address $billing_address */
    $billing_address = $order->getBillingProfile()->get('address')->first();

    return [
      'address_1' => $billing_address->getAddressLine1(),
      'address_2' => $billing_address->getAddressLine2(),
      'city' => $billing_address->getLocality(),
      'province' => $billing_address->getAdministrativeArea(),
      'country' => $billing_address->getCountryCode(),
      'postal_code' => $billing_address->getPostalCode(),
    ];
  }

}
