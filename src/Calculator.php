<?php

namespace RoyalMailPriceCalculator;

use RoyalMailPriceCalculator\Exceptions\UnknownPackageTypeException;

/**
 * Class Calculator
 * @package RoyalMailPriceCalculator
 */
class Calculator
{
	private $now;
	private $iso_code = 'uk';

	public function __construct()
	{
		$this->now = new \DateTime();
	}

	/**
	 * @var \RoyalMailPriceCalculator\Services\Service[]
	 */
	private $services;

	/**
	 * @return \RoyalMailPriceCalculator\Services\Service[]
	 */
	public function getServices()
	{
		return $this->services;
	}

	/**
	 * @param \RoyalMailPriceCalculator\Services\Service[] | \RoyalMailPriceCalculator\Services\Service $services
	 */
	public function setServices($services)
	{
		if (is_array($services)) {
			$this->services = $services;
		} else {
			$this->services = array($services);
		}
	}

	public function setCountryCode($iso2code){
		$this->iso_code = $iso2code;
	}

	/**
	 * @param \RoyalMailPriceCalculator\Package $package
	 * @return array
	 * @throws \Exception
	 */
	public function calculatePrice(Package $package)
	{
		$services = $this->getServices();

		$calculatedPrices = array();

		foreach ($services as $service) {
			$service->setZone($this->iso_code);
			$priceData = $service->getPriceData();
			$prices = array();

			try {
				$packageType = $service->getPackageType($package);

				foreach ($priceData as $data) {

					if ($packageType === false) {
						$packageTypePrices = $data['prices'];
					} else {
						$packageTypePrices = $data['prices'][$packageType];
					}

					ksort($packageTypePrices);

					$packagePrice = 0;
					foreach ($packageTypePrices as $weight => $price) {
						if ($weight >= $package->getWeight()) {
							$packagePrice = $price;
							break;
						}
					}
					if(!$packagePrice) continue;
					$prices[] = array(
						'price' => number_format($packagePrice, 2, '.', ''),
						'compensation' => $data['compensation']
					);


				}
			} catch (UnknownPackageTypeException $e) {
			}

			$calculatedPrices[] = array(
				'service' => $service,
				'prices' => $prices
			);
		}

		return $calculatedPrices;
	}

	public static function get_region_code($iso2code){
		switch(strtolower($iso2code)){
			case 'uk':
			case 'gb':
				return 'uk';
			case 'al':
			case 'ad':
			case 'am':
			case 'at':
			case 'az':
			case 'by':
			case 'be':
			case 'ba':
			case 'bg':
			case 'hr':
			case 'cy':
			case 'cz':
			case 'dk':
			case 'ee':
			case 'fo':
			case 'fi':
			case 'fr':
			case 'ge':
			case 'de':
			case 'gi':
			case 'gr':
			case 'gl':
			case 'hu':
			case 'is':
			case 'ie':
			case 'it':
			case 'kz':
			case 'xk':
			case 'kg':
			case 'lv':
			case 'lt':
			case 'li':
			case 'lu':
			case 'mk':
			case 'mt':
			case 'md':
			case 'mc':
			case 'me':
			case 'nl':
			case 'no':
			case 'pl':
			case 'pt':
			case 'ro':
			case 'ru':
			case 'sm':
			case 'rs':
			case 'sk':
			case 'si':
			case 'es':
			case 'se':
			case 'ch':
			case 'tj':
			case 'tr':
			case 'tm':
			case 'ua':
			case 'uz':
			case 'va':
				return 'eu';
			case 'au':
			case 'cx':
			case 'fj':
			case 'ki':
			case 'nz':
			case 'aq':
			case 'sg':
			case 'to':
			case 'cc':
			case 'pf':
			case 'mo':
			case 'pg':
			case 'sb':
			case 'tv':
			case 'io':
			case 'ck':
			case 'nr':
			case 'nu':
			case 'la':
			case 'as':
			case 'nc':
			case 'nf':
			case 'pn':
			case 'tk':
			case 'ws':
				return 'intl_2';
			default:
				return 'intl_1';
		}
	}
}
