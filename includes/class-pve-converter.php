<?php
/**
* PVE_Converter - Price Conversion
*
* Handles all ETH arithmetic. This means converting a USD amount to ETH using bcmath at 18 decimal precision, 
* and encoding the order ID fingerprint into the minor decimal places of the ETH amount. It does not fetch prices, 
* it does not know what the current ETH/USD rate is, and it does not write anything to the database. 
* It receives numbers and returns numbers. Nothing else.
* 
* Hooks registered:
*
* Options read: // via get_option()
* Options written: // via update_option() or add_option()
*
* Order meta read: // via get_post_meta()
* Order meta written: // via update_post_meta()
*
* Constants defined:
* Files loaded:
*
* @package Payments_Via_Ethereum
* @since 1.420.69
*/

//ABSPATH guard, must be first executable line, no exceptions
defined( 'ABSPATH' ) || exit;

class PVE_Converter
{
	private $unitMap = [
		'wei' => '1',
		'kwei' => '1000',
		'Kwei' => '1000',
		'babbage' => '1000',
		'femtoether' => '1000',
		'mwei' => '1000000',
		'lovelace' => '1000000',
		'picoether' => '1000000',
		'gwei' => '1000000000',
		'shannon' => '1000000000',
		'nanoether' => '1000000000',
		'nano' => '1000000000',
		'szabo' => '1000000000000',
		'microether' => '1000000000000',
		'micro' => '1000000000000',
		'finney' => '1000000000000000',
		'milliether' => '1000000000000000',
		'milli' => '1000000000000000',
		'ether' => '1000000000000000000',
		'kether' => '1000000000000000000000',
		'grand' => '1000000000000000000000',
		'einstein' => '1000000000000000000000',
		'mether' => '1000000000000000000000000',
		'gether' => '1000000000000000000000000000',
		'tether' => '1000000000000000000000000000000'
	];
	
	public function pve_fromWei(string $amount, string $unit = 'ether'): string{
		if ($unit == 'wei') {
			return $amount;
		}
		return bcdiv($amount, $this->getValueOfUnit($unit), $this->getDivisionScale($amount, $unit));
	}

	public function pve_toWei(string $amount, string $unit = 'ether'): string{
		if ($unit == 'wei') {
			return $amount;
		}
		return bcmul($amount, $this->getValueOfUnit($unit));
	}

	private function pve_getValueOfUnit(string $unit = 'ether'){
		if (!isset($this->unitMap[$unit])) {
			$this->throwExceptionForUnit($unit);
		}

		return $this->unitMap[$unit];
	}

	private function pve_getDivisionScale(string $amount, string $unit){
		if (!isset($this->unitMap[$unit])) {
			$this->throwExceptionForUnit($unit);
		}
		$zeroes = substr_count($this->unitMap[$unit], 0);
		$decimals = strlen($amount) - strpos($amount, '.') - 1;

		return $zeroes + $decimals;
	}

	private function pve_throwExceptionForUnit(string $unit){
		$message = sprintf('A unit "%s" doesn\'t exist, please use the one of the following units: %s', $unit, implode(',', array_keys($this->unitMap)));

		throw new \UnexpectedValueException($message);
	}
}
