You can sign up for a Printfection account at http://printfection.com.

## Requirements

PHP 5.2 and later.

## Composer

You can install the bindings via Composer[http://getcomposer.org/]. Add this to your +composer.json+:

    {
      "require": {
        "printfection/printfection-php": "1.*"
      }
    }
    
Then install via:

    composer.phar install

To use the bindings, either user Composer's autoload[https://getcomposer.org/doc/00-intro.md#autoloading]:

    require_once('vendor/autoload.php');
    
Or manually:

    require_once('/path/to/vendor/printfection/printfection-php/lib/Printfection.php');

## Manual Installation

Obtain the latest version of the Printfection PHP bindings with:

    git clone https://github.com/printfection/printfection-php

To use the bindings, add the following to your PHP script:

    require_once("/path/to/printfection-php/lib/Printfection.php");

## Getting Started

Simple usage looks like:

    Printfection::setApiKey('ACCESS_TOKEN');

    $order = Printfection_Order::create(array(
        'campaign_id' => 1,
        'ship_to' => array(
                'name' => 'Herman Munster',
                'address' => '1313 Mockingbird Lane',
                'city' => 'Mockingbird Heights',
                'state' => 'California',
                'zip' => '90210',
                'country' => 'US',
                'email' => 'herman@printfection.com'
            ),
        'lineitems' => array(
                array(
                        'item_id' => 1,
                        'size_id' => 1,
                        'quantity' => 13
                    )
            )
    ));

    echo $order;

## Documentation

Please see http://printfection.github.io/API-Documentation/ for up-to-date documentation.
