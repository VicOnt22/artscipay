<?php

namespace Drupal\commerce_moneris_checkout;

/**
 * Moneris Checkout Response Codes.
 */
final class MonerisCheckoutResponseCodes {

  /**
   * Success.
   */
  const MONERIS_CHECKOUT_SUCCESS = '001';

  /**
   * 3-D Secure failed on response.
   */
  const MONERIS_CHECKOUT_3D_SECURE_FAILED = '902';

  /**
   * Invalid ticket.
   */
  const MONERIS_CHECKOUT_INVALID_TICKET = '2001';

  /**
   * Ticket re-use.
   */
  const MONERIS_CHECKOUT_TICKET_REUSE = '2002';

  /**
   * Ticket expired.
   */
  const MONERIS_CHECKOUT_TICKET_EXPIRED = '2003';

}
