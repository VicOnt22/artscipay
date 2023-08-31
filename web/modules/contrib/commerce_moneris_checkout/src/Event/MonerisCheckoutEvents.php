<?php

namespace Drupal\commerce_moneris_checkout\Event;

/**
 * Commerce Moneris Checkout events class.
 */
final class MonerisCheckoutEvents {

  /**
   * Payment/transaction complete.
   */
  const MCO_TRANSACTION_COMPLETE = 'commerce_moneris_checkout.transaction_complete';

  /**
   * Payment/transaction cancel.
   */
  const MCO_TRANSACTION_CANCEL = 'commerce_moneris_checkout.transaction_cancel';

}
