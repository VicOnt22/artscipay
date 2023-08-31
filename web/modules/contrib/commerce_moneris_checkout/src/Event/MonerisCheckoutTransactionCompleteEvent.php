<?php

namespace Drupal\commerce_moneris_checkout\Event;

use Drupal\commerce_payment\Entity\PaymentInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * MCO Transaction complete event.
 */
class MonerisCheckoutTransactionCompleteEvent extends Event {

  /**
   * The payment.
   *
   * @var \Drupal\commerce_payment\Entity\PaymentInterface
   */
  protected $payment;

  /**
   * The constructor.
   *
   * @param \Drupal\commerce_payment\Entity\PaymentInterface $payment
   *   The payment entity.
   */
  public function __construct(PaymentInterface $payment) {
    $this->payment = $payment;
  }

  /**
   * Get the Payment entity.
   *
   * @return \Drupal\commerce_payment\Entity\PaymentInterface|null
   *   Returns the Payment entity.
   */
  public function getPayment(): ?PaymentInterface {
    return $this->payment;
  }

}
