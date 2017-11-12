Royal Mail Price Calculator
===========================

This library is forked to revive it.

It allows you to calculate the cost of sending a package with Royal Mail, updated prices and extends support to all package prices.

Usage
-----
Install the latest version with `composer require alexedimensionz/royal-mail-price-calculator`

Main Changes from Justin Hook's Repo
------------------------------------
- Removed Doctrine requirement
- Added almost all shipping types
- Added International shipping options and prices
- Updated price list to March, 2017 (latest list as of November, 2017)

Supported Services
------------------
Service  | Class
------------- | -------------
1st Class Service | `FirstClassService()`
2nd Class Service | `SecondClassService()`
Signed For 1st Class | `SignedForFirstClassService()`
Signed For 2nd Class | `SignedForSecondClassService()`
Guaranteed by 9am | `GuaranteedByNineAmService()`
Guaranteed by 9am with Saturday Guarantee | `GuaranteedByNineAmWithSaturdayService()`
Guaranteed by 1pm | `GuaranteedByOnePmService()`
Guaranteed by 1pm with Saturday Guarantee | `GuaranteedByOnePmWithSaturdayService()`
International Economy | `InternationalEconomy()`
International Standard | `InternationalStandard()`
International Signed | `InternationalSigned()`
International Tracked | `InternationalTracked()`
International Tracked And Signed | `InternationalTrackedAndSigned()`


Example for UK Delivery Targets
-------------------------------
```php
<?php

require 'vendor/autoload.php';

use \RoyalMailPriceCalculator\Calculator;
use \RoyalMailPriceCalculator\Package;
use \RoyalMailPriceCalculator\Services\GuaranteedByOnePmService;
use \RoyalMailPriceCalculator\Services\FirstClassService;

$calculator = new Calculator();

$package = new Package();
$package->setDimensions(15, 15, 0.4);
$package->setWeight(90);

$calculator->setServices(array(
							new FirstClassService(), 
							new GuaranteedByOnePmService()));

foreach ($calculator->calculatePrice($package) as $calculated)
{
    echo $calculated['service']->getName() . "\n";
    foreach ($calculated['prices'] as $price) {
        echo "  →  £{$price['price']} (Compensation: £{$price['compensation']})\n";
    }
    echo "\n";
}
```

Will output something like:
```
1st Class Service
  →  £0.62 (Compensation: £20)

Guaranteed by 1pm
  →  £6.40 (Compensation: £500)
  →  £7.40 (Compensation: £1000)
  →  £9.40 (Compensation: £2500)
```

Example for International Delivery Targets
------------------------------------------
```php
<?php

require 'vendor/autoload.php';

use \RoyalMailPriceCalculator\Calculator;
use \RoyalMailPriceCalculator\Package;
use \RoyalMailPriceCalculator\Services\InternationalTracked;
use \RoyalMailPriceCalculator\Services\InternationalEconomy;

$calculator = new Calculator();

$package = new Package();
$package->setDimensions(15, 15, 0.4);
$package->setWeight(90);

// This part is mandatory for international shipments
$target_iso = 'US';
$calculator->setCountryCode($target_iso);
//


$calculator->setServices(array(
                         	new InternationalTracked(), 
                         	new InternationalEconomy()));


// Note: there is no compensation value for international
foreach ($calculator->calculatePrice($package) as $calculated)
{
    echo $calculated['service']->getName() . "\n";
    foreach ($calculated['prices'] as $price) {
        echo "  →  £{$price['price']}\n";
    }
    echo "\n";
}
```

Will output something like:
```
International Tracked
  →  £8.50

International Economy
  →  £13.30
```


Useful Functions
----------------

Royal Mail has 4 delivery zones:
- UK
- Europe
- International (Zone 1)
- International (Zone 2)

You can find the zone code for your country by using the 2-Letter ISO code.

```php
<?php

require 'vendor/autoload.php';

use \RoyalMailPriceCalculator\Calculator;

?>
CA region is: <?php echo Calculator::get_region_code('CA'); ?><br/>
US region is: <?php echo Calculator::get_region_code('US'); ?><br/>
GB region is: <?php echo Calculator::get_region_code('GB'); ?><br/>
AU region is: <?php echo Calculator::get_region_code('AU'); ?><br/>
DE region is: <?php echo Calculator::get_region_code('AU'); ?>
```

Will output:
```
CA region is: intl_1
US region is: intl_1
GB region is: uk
AU region is: intl_2 
DE region is: eu

```