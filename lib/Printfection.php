<?php
/**
 * This entire library is heavily influenced (and built from) Stripe's PHP API SDK
 *
 * @link https://github.com/stripe/stripe-php/
 */

// Tested on PHP 5.2, 5.3
// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('Printfection needs the CURL PHP extension.');
}

if (!function_exists('json_decode')) {
  throw new Exception('Printfection needs the JSON PHP extension.');
}

if (!function_exists('mb_detect_encoding')) {
  throw new Exception('Printfection needs the Multibyte String PHP extension.');
}

// Printfection singleton
require(dirname(__FILE__) . '/Printfection/Printfection.php');

// Utilities
require(dirname(__FILE__) . '/Printfection/Util.php');
require(dirname(__FILE__) . '/Printfection/Util/Set.php');

// Errors
require(dirname(__FILE__) . '/Printfection/Error.php');
require(dirname(__FILE__) . '/Printfection/ApiError.php');
require(dirname(__FILE__) . '/Printfection/ApiConnectionError.php');
require(dirname(__FILE__) . '/Printfection/AuthenticationError.php');
require(dirname(__FILE__) . '/Printfection/InvalidRequestError.php');

// Plumbing
require(dirname(__FILE__) . '/Printfection/Object.php');
require(dirname(__FILE__) . '/Printfection/ApiRequestor.php');
require(dirname(__FILE__) . '/Printfection/ApiResource.php');
require(dirname(__FILE__) . '/Printfection/List.php');

// Printfection API Resources
require(dirname(__FILE__) . '/Printfection/Campaign.php');
require(dirname(__FILE__) . '/Printfection/Image.php');
require(dirname(__FILE__) . '/Printfection/Item.php');
require(dirname(__FILE__) . '/Printfection/LineItem.php');
require(dirname(__FILE__) . '/Printfection/Order.php');
require(dirname(__FILE__) . '/Printfection/Size.php');
