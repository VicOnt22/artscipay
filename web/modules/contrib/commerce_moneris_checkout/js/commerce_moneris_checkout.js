/**
 * @file
 * Defines behaviors for the Moneris checkout payment method form.
 */

(function ($, Drupal, drupalSettings, once) {

  'use strict';

  Drupal.behaviors.commerce_moneris_checkout = {
    attach: function (context, settings) {
      // If no MCO settings or ticket, halt.
      if (!settings.commerceMonerisCheckout || !settings.commerceMonerisCheckout.ticket) {
        return;
      }

      // Debug settings.
      if (settings.commerceMonerisCheckout.debug) {
        console.log(settings.commerceMonerisCheckout);
      }

      // Find MCO forms. Should only be one in most cases.
      $(once('commerce-moneris-checkout-form-processed', '.commerce-moneris-checkout-form', context)).each(function () {
        // Initialize.
        var mco = new monerisCheckout();

        // Set mode.
        if (settings.commerceMonerisCheckout.mode) {
          mco.setMode(settings.commerceMonerisCheckout.mode);
        }

        // Set checkout div.
        if (settings.commerceMonerisCheckout.div) {
          mco.setCheckoutDiv(settings.commerceMonerisCheckout.div);
        }

        // Set callbacks.
        mco.setCallback("page_loaded", mcoPageLoad);
        mco.setCallback("cancel_transaction", mcoCancelTransaction);
        mco.setCallback("error_event", mcoErrorEvent);
        mco.setCallback("payment_complete", mcoPaymentComplete);

        // Start checkout.
        mco.startCheckout(settings.commerceMonerisCheckout.ticket);

        /**
         * Page load event handler.
         *
         * @param event
         */
        function mcoPageLoad(event) {
          var response = jQuery.parseJSON(event);
          response.response_state = 'page_load';

          if (settings.commerceMonerisCheckout.debug) {
            console.log('MCO is now loaded.');
            console.log(response);
          }

          // Make sure we have successful response code.
          if (response && response.response_code !== '001') {
            // Bad response code.

            // Close checkout.
            mco.closeCheckout(settings.commerceMonerisCheckout.ticket);

            // Make cancel return request.
            window.location.href = mcoGetCancelUrl(response);
          }
        }

        /**
         * Cancel transaction event handler.
         *
         * @param event
         */
        function mcoCancelTransaction(event) {
          var response = jQuery.parseJSON(event);
          response.response_state = 'cancel';

          if (settings.commerceMonerisCheckout.debug) {
            console.log('Cancel transaction callback.');
            console.log(response);
          }

          // Close checkout.
          mco.closeCheckout(settings.commerceMonerisCheckout.ticket);

          // Make cancel return request.
          window.location.href = mcoGetCancelUrl(response);
        }

        /**
         * Error event handler.
         *
         * @param event
         */
        function mcoErrorEvent(event) {
          var response = jQuery.parseJSON(event);
          response.response_state = 'error';

          if (settings.commerceMonerisCheckout.debug) {
            console.log('Error event callback.');
            console.log(response);
          }

          // Close checkout.
          mco.closeCheckout(settings.commerceMonerisCheckout.ticket);

          // Make cancel return request.
          window.location.href = mcoGetCancelUrl(response);
        }

        /**
         * Payment complete handler.
         *
         * @param event
         */
        function mcoPaymentComplete(event) {
          var response = jQuery.parseJSON(event);
          response.response_state = 'complete';

          if (settings.commerceMonerisCheckout.debug) {
            console.log('Payment complete callback.');
            console.log(response);
          }

          // Close checkout.
          mco.closeCheckout(settings.commerceMonerisCheckout.ticket);

          // Make return request.
          window.location.href = mcoGetReturnUrl(response);
        }
      });

      /**
       * Get the return url.
       *
       * @param response
       * @returns string
       */
      function mcoGetReturnUrl(response) {
        var return_url = settings.commerceMonerisCheckout.return_url;

        // Set ticket.
        if (response.ticket) {
          return_url += '?ticket=' + response.ticket;
        }

        // Set response code.
        if (response.response_code) {
          return_url += '&response_code=' + response.response_code;
        }

        // Set response state.
        if (response.response_state) {
          return_url += '&response_state=' + response.response_state;
        }

        if (settings.commerceMonerisCheckout.debug) {
          console.log(return_url);
        }

        return return_url;
      }

      /**
       * Get the cancel url.
       *
       * @param response
       * @returns string
       */
      function mcoGetCancelUrl(response) {
        var cancel_url = settings.commerceMonerisCheckout.cancel_url;

        // Set ticket.
        if (response.ticket) {
          cancel_url += '?ticket=' + response.ticket;
        }

        // Set response code.
        if (response.response_code) {
          cancel_url += '&response_code=' + response.response_code;
        }

        // Set response state.
        if (response.response_state) {
          cancel_url += '&response_state=' + response.response_state;
        }

        if (settings.commerceMonerisCheckout.debug) {
          console.log(cancel_url);
        }

        return cancel_url;
      }
    }
  };

})(jQuery, Drupal, drupalSettings, once);
