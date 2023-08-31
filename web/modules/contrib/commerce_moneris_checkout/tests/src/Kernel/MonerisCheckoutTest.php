<?php

namespace Drupal\Tests\commerce_moneris_checkout\Kernel;

use Drupal\commerce_moneris_checkout\Plugin\Commerce\PaymentGateway\MonerisCheckout;
use Drupal\commerce_payment\Entity\PaymentGateway;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests the Moneris Checkout payment gateway.
 *
 * @group commerce_moneris_checkout
 */
class MonerisCheckoutTest extends CommerceKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'profile',
    'entity_reference_revisions',
    'state_machine',
    'commerce_number_pattern',
    'commerce_order',
    'commerce_payment',
    'commerce_moneris_checkout',
  ];

  /**
   * The test gateway.
   *
   * @var \Drupal\commerce_payment\Entity\PaymentGateway
   */
  protected $gateway;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('profile');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installEntitySchema('commerce_payment');
    $this->installEntitySchema('commerce_payment_method');
    $this->installConfig('commerce_order');
    $this->installConfig('commerce_payment');

    /** @var \Drupal\commerce_payment\Entity\PaymentGateway $gateway */
    $gateway = PaymentGateway::create([
      'id' => 'moneris_checkout',
      'label' => 'moneris_checkout',
      'plugin' => 'moneris_checkout',
    ]);
    $gateway->getPlugin()->setConfiguration([
      'store_id' => 'monca07553',
      'api_token' => 'QAZhHds8RGFZFCGb4euC',
      'checkout_id' => 'chktSQJ3U07553',
      'mode' => 'qa',
      'country_code' => 'CA',
      'display_label' => 'Moneris Checkout',
      'api_logging' => [
        'request' => 'request',
        'response' => 'response',
      ],
      'payment_method_types' => ['credit_card'],
    ]);
    $gateway->save();
    $this->gateway = $gateway;
  }

  /**
   * Test the Moneris Checkout payment gateway.
   */
  public function testGatewayConstruction() {
    $plugin = $this->gateway->getPlugin();
    $this->assertTrue($plugin instanceof MonerisCheckout);
  }

}
