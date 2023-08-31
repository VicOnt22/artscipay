<?php

namespace Drupal\commerce_moneris_checkout\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface;

/**
 * Provides the interface for the Moneris Checkout payment gateway.
 */
interface MonerisCheckoutInterface extends OffsitePaymentGatewayInterface {

  /**
   * Get the Moneris store_id key for the payment gateway.
   *
   * @return string
   *   Returns the store id for merchant account.
   */
  public function getStoreId();

  /**
   * Get the Moneris api_token for the payment gateway.
   *
   * @return string
   *   Returns the API token for merchant account.
   */
  public function getApiToken();

  /**
   * Get the Moneris checkout_id for the payment gateway.
   *
   * @return string
   *   Returns the configuration id for checkout form.
   */
  public function getCheckoutId();

  /**
   * Get the Moneris country code for the payment gateway.
   *
   * @return string
   *   Returns the configured country code.
   */
  public function getCountryCode();

  /**
   * Get the Moneris order number strategy for the payment gateway.
   *
   * @return string
   *   Returns the configured order number strategy.
   */
  public function getOrderNumberStrategy();

  /**
   * Get the receipt.
   *
   * @param string $ticket
   *   The MCO ticket id.
   *
   * @return array|null
   *   Returns the receipt request for a specific ticket.
   */
  public function getReceipt(string $ticket): ?array;

  /**
   * Gets the Moneris checkout gateway.
   *
   * @return string
   *   Returns the checkout gateway.
   */
  public function getCheckoutGateway();

  /**
   * Submits an API request to MonerisCheckout.
   *
   * @param array $body
   *   The body of the request.
   *
   * @return array
   *   The response data.
   */
  public function apiRequest(array $body): ?array;

  /**
   * Gets the order number.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order.
   *
   * @return string
   *   Returns the order number.
   */
  public function getOrderNumber(OrderInterface $order);

}
