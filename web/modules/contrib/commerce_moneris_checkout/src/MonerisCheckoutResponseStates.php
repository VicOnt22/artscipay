<?php

namespace Drupal\commerce_moneris_checkout;

/**
 * Moneris Checkout Response States.
 */
final class MonerisCheckoutResponseStates {

  /**
   * Complete response state.
   */
  const MONERIS_CHECKOUT_COMPLETE = 'complete';

  /**
   * Cancel response state.
   */
  const MONERIS_CHECKOUT_CANCEL = 'cancel';

  /**
   * Error response state.
   */
  const MONERIS_CHECKOUT_ERROR = 'error';

}
