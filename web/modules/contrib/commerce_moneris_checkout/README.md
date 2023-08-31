# Commerce Moneris Checkout

## Introduction

Commerce Moneris Checkout provides a payment gateway plugin that embeds the
Moneris Checkout iframe into the Payment checkout step. It leverages the
`OffsitePaymentGateway` functionality of Commerce Core to achieve this. This
gateway requires a specific Moneris Checkout profile configuration in your
Moneris account as outlined below.

## Requirements

You must have a Moneris account with a valid Moneris Checkout profile to use
this module in production. However, you can use shared test account details to
try the system out before going live. Currently only supports USA or Canada.

This module depends on a forked version of the Moneris SDK that must be
installed via Composer. This version of the SDK is similar to Moneris's
official SDK but includes support for Composer and more recent PHP versions.

For more information see: https://packagist.org/packages/smmccabe/moneris

## Installation

Install via Composer so you get the appropriate dependencies, e.g.:

`composer require 'drupal/commerce_moneris_checkout:^1.0'`

## Configuration

To start collecting payment via Moneris Checkout, you will need to configure a
profile in your Moneris account and then create a payment gateway in your
Drupal Commerce site. The following instructions are for the shared test
environment, though they should apply to a live account as well.

You can find relevant links, login and API credentials, and test card numbers
at the following page: https://developer.moneris.com/en/More/Testing/Testing%20a%20Solution

1. Open the test environment of the Merchant Resource Center: https://esqa.moneris.com/mpg/
2. Username: `demouser`, Store ID: `store3`, Password: `password`. (If the iframe does not work for some reason, you might try a different store ID; `store3` seems to work consistently.)
3. Under `Admin > Moneris Checkout Config`, which should be the opening page, click the `Create Profile` button.
4. Click the `Edit Name` link and name it something unique. Bear in mind that all users of the shared test environment will see this, so do not accidentally disclose private information.
5. Change the `Checkout Type` to the second "custom form" option.
6. Adjust the `Payment` settings as desired, but note that the module is currently only tested against the purchase `Transaction Type` with no tokenization.
7. The iframe looks best on a white background when you adjust the `Branding & Design` settings so the `Header Background` is `FFFFFF` and the `Background` is set to white. In order to position the iframe within the primary content region, disable `Enable Fullscreen` checkbox.
8. To properly redirect from the `Payment` step in Drupal Commerce to the `Complete` step, you must adjust the `Order Confirmation` settings to `Use Own Page`.
9. Finally, you likely want to disable the `Approved Transactions` checkbox in the `Email Communications` in favor of the checkout completion email provided by Drupal Commerce, though you can use both emails if desired.

Next, you must create the payment gateway configuration within Drupal Commerce.
Name it whatever you want, though we recommend for the display name that you
pick something more intelligible to your customers like "Credit card" or
something similar.

If you are using the test account and credentials above, you'll use the
following configuration options:

* Environment: Testing
* Store ID: store3
* API Token: yesguy
* Checkout ID: (find this at the top of your profile configuration, a string like `chkt7MQ5Btore1`)
* Country: Canada or US per your requirements

A quick note about the order number strategy: if Moneris detects you attempting
to use the same order number twice, it will generate an error and not allow you
to checkout. During testing, we recommend using the strategy with the timestamp
appended to avoid duplicates until you're ready to go live. Then you can decide
either to retain that strategy or switch to using the Order ID, which will not
necessarily match the Order Number shown in the Commerce UI, or generating an
Order Number early and using that, which will lead to gaps in your numbers. If
you'd like to see an additional strategy included, you can suggest it as a
feature request on drupal.org.

## Testing

Find test credit card numbers on the following page:
https://developer.moneris.com/More/Testing/Testing%20a%20Solution

## Troubleshooting

The test environment is not guaranteed to be fully available. The instructions
on the page indicate that test transactions must be beneath $11, but this
module has passed tests for a wide variety of much higher amounts. That said,
if you get a random failure, try a smaller transaction just in case.

## Developer documentation

Full developer documentation can be found at:
https://developer.moneris.com/livedemo/checkout/overview/guide/dotnet
