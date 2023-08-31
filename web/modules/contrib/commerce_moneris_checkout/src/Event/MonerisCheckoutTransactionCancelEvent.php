<?php

namespace Drupal\commerce_moneris_checkout\Event;

use Drupal\commerce_order\Entity\OrderInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * MCO Transaction cancel event.
 */
class MonerisCheckoutTransactionCancelEvent extends Event {

  /**
   * The order.
   *
   * @var \Drupal\commerce_order\Entity\OrderInterface
   */
  protected $order;

  /**
   * The constructor.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order entity.
   */
  public function __construct(OrderInterface $order) {
    $this->order = $order;
  }

  /**
   * Get the Order entity.
   *
   * @return \Drupal\commerce_order\Entity\OrderInterface|null
   *   Returns the Order entity.
   */
  public function getOrder(): ?OrderInterface {
    return $this->order;
  }

}
