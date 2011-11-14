<?php
/*************************************************************************************************
 * LiveCart																					  *
 * Copyright (C) 2007-2009 UAB "Integry Systems" (http://livecart.com)							*
 * All rights reserved																		   *
 *																							   *
 * This source file is a part of LiveCart software package and is protected by LiveCart license. *
 * The license text can be found in the license.txt file. In case you received a package without *
 * a license file, the license text is also available online at http://livecart.com/license	  *
 *************************************************************************************************/

/**
 * Defines a system currency. There can be multiple currencies active at the same time.
 * This allows to define product prices in different currencies or convert the prices
 * automatically using the currency rates. In addition to product prices, shipping rates,
 * taxes and other charges can also be converted to other currencies using the currency rates.
 *
 * @package application.model
 * @author Integry Systems <http://integry.com>
 */
class Currency extends ActiveRecordModel
{
	const NO_ROUNDING = 'NONE';
	const ROUND = 'ROUND';
	const ROUND_UP = 'ROUND_UP';
	const ROUND_DOWN = 'ROUND_DOWN';
	const TRIM = 'TRIM';
	const TRIM_UP = 'TRIM_UP';
	const TRIM_DOWN = 'TRIM_DOWN';

	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName("Currency");

		$schema->registerField(new ARPrimaryKeyField("ID", ArChar::instance(3)));

		$schema->registerField(new ARField("rate", ArFloat::instance(16)));
		$schema->registerField(new ARField("lastUpdated", ArDateTime::instance()));
		$schema->registerField(new ARField("isDefault", ArBool::instance()));
		$schema->registerField(new ARField("isEnabled", ArBool::instance()));
		$schema->registerField(new ARField("position", ARInteger::instance()));
		$schema->registerField(new ARField("pricePrefix", ARText::instance(20)));
		$schema->registerField(new ARField("priceSuffix", ARText::instance(20)));
		$schema->registerField(new ARField("decimalSeparator", ARVarchar::instance(3)));
		$schema->registerField(new ARField("thousandSeparator", ARVarchar::instance(3)));
		$schema->registerField(new ARField("decimalCount", ARInteger::instance()));
		$schema->registerField(new ARField("rounding", ARText::instance()));
	}

	/*####################  Static method implementations ####################*/

	public static function getInstanceById($id, $loadData = true)
	{
		return ActiveRecordModel::getInstanceById(__CLASS__, $id, $loadData);
	}

	public static function getNewInstance($id)
	{
		$inst = parent::getNewInstance(__class__);
		$inst->setID($id);
		return $inst;
	}

	/*####################  Instance retrieval ####################*/

	/**
	 *  Return Currency instance by ID and provide additional validation. If the currency doesn't exist
	 *  or is not valid, instance of the default currency is returned.
	 *
	 *  @return Currency
	 */
	public static function getValidInstanceById($id, $loadData = true)
	{
		try
		{
			$instance = ActiveRecordModel::getInstanceById(__CLASS__, $id, $loadData);
		}
		catch (ARNotFoundException $e)
		{
			$instance = null;
		}

		if (!$instance || !$instance->isEnabled->get())
		{
			$instance = self::getApplication()->getDefaultCurrency();
		}

		return $instance;
	}

	/*####################  Value retrieval and manipulation ####################*/

	public function setAsDefault($default = true)
	{
	  	$this->isDefault->set((bool)$default);
	}

	public function isDefault()
	{
	  	return $this->isDefault->get();
	}

	public function getFormattedPrice($price)
	{
		if (!$this->isLoaded())
		{
			$this->load();
		}

		$isNegative = ($price < 0);
		if ($isNegative)
		{
			$price = abs($price);
		}

		$number = $this->round($price);
		$number = number_format($number, !is_null($this->decimalCount->get()) ? $this->decimalCount->get() : 2, $this->decimalSeparator->get(), $this->thousandSeparator->get());

		return ($isNegative ? '-' : '') . $this->pricePrefix->get() . $number . $this->priceSuffix->get();
	}

	public function round($price)
	{
		if (!$this->isLoaded())
		{
			try
			{
				$this->load();
			}
			catch (ARNotFoundException $e)
			{
				// do nothing?
				$this->markAsLoaded();
			}
		}

		$number = number_format($price, !is_null($this->decimalCount->get()) ? $this->decimalCount->get() : 2, '.', '');

		return $number;
	}

	public function roundPrice($price)
	{
		$rounding = $this->getRoundingRange($price);

		if ($rounding)
		{
			$precision = $rounding['precision'];
			switch ($rounding['type'])
			{
				case 'ROUND':
					$price = $this->roundToNearest($price, $precision);
					break;
				case 'ROUND_DOWN':
					$price = $this->roundToNearest($price, $precision, 'floor');
					break;
				case 'ROUND_UP':
					$price = $this->roundToNearest($price, $precision, 'ceil');
					break;
				case 'TRIM':
					$price = $this->trimToNearest($price, $precision);
					break;
				case 'TRIM_DOWN':
					$price = $this->trimToNearest($price, $precision, 'floor');
					break;
				case 'TRIM_UP':
					$price = $this->trimToNearest($price, $precision, 'ceil');
					break;
			}
		}

		return $this->round($price);
	}

	public function convertAmountFromDefaultCurrency($amount)
	{
		if ($this->isDefault->get())
		{
			return $amount;
		}

		$rate = $this->rate->get();
		return $amount / (empty($rate) ? 1 : $rate);
	}

	public function convertAmountToDefaultCurrency($amount)
	{
		if ($this->isDefault->get())
		{
			return $amount;
		}

		$rate = $this->rate->get();
		return $amount * (empty($rate) ? 1 : $rate);
	}

	public function convertAmount(Currency $currency, $amount)
	{
		$amount = $currency->convertAmountToDefaultCurrency($amount);
		return $this->convertAmountFromDefaultCurrency($amount);
	}

	public function setRoundingRule($range, $type, $precision = null)
	{
		$rounding = unserialize($this->rounding->get());
		$rounding[] = array('range' => $range, 'type' => $type, 'precision' => $precision);
		$this->rounding->set(serialize($rounding));
	}

	public function clearRoundingRules()
	{
		$this->roundingRules = null;
		$this->rounding->setNull();
	}

	/*####################  Data array transformation ####################*/

	public static function transformArray($array, ARSchema $schema)
	{
		$array = parent::transformArray($array, $schema);
		$array['name'] = self::getApplication()->getLocale()->info()->getCurrencyName($array['ID']);
		$array['format'] = $array['pricePrefix'] . '%d.' . $array['decimalCount'] . 'f' . $array['priceSuffix'];
		$array['rounding'] = unserialize($array['rounding']);
		return $array;
	}

	/*####################  Saving ####################*/

	public static function deleteById($id)
	{
		self::deleteCache();

		// make sure the currency record exists
		$inst = ActiveRecord::getInstanceById('Currency', $id, true);

		// make sure it's not the default currency
		if (true != $inst->isDefault->get())
		{
			ActiveRecord::deleteByID('Currency', $id);
			return true;
		}
		else
		{
		  	return false;
		}
	}

	public static function deleteCache()
	{
		$cacheFile = self::getCacheFile();
		if (file_exists($cacheFile))
		{
			unlink($cacheFile);
		}
	}

	public static function getCacheFile()
	{
		return ClassLoader::getRealPath('cache') . '/currencies.php';
	}

	public function save($forceOperation = 0)
	{
		// do not allow 0 rates
		if (!$this->rate->get())
		{
			$this->rate->set(1);
		}

		self::deleteCache();

		return parent::save($forceOperation);
	}

	protected function insert()
	{
	  	// check currency symbol
	  	if (!$this->pricePrefix->get() && !$this->priceSuffix->get())
	  	{
			$prefixes = include ClassLoader::getRealPath('installdata.currency.signs') . '.php';
			if (isset($prefixes[$this->getID()]))
			{
				$signs = $prefixes[$this->getID()];

				$this->pricePrefix->set($signs[0]);

				if (isset($signs[1]))
				{
					$this->priceSuffix->set($signs[1]);
				}
			}
		}

		// check if default currency exists
		if (!ActiveRecord::getRecordSet('Currency', select(eq('Currency.isDefault', 1)))->getTotalRecordCount())
		{
			$this->isDefault->set(true);
			$this->isEnabled->set(true);
		}

		$this->setLastPosition();

		return parent::insert();
	}

	private function roundToNearest($number, $nearest, $type = null)
	{
		$negative = false;

		if ($number < 0)
		{
			$negative = true;
		}

		$number = abs($number);
		$nearest = abs($nearest);

		if ($number <= $nearest)
		{
			$result = $nearest;
		}
		else
		{
			if (!(float)$nearest)
			{
				$nearest = 1;
			}

			switch ($type)
			{
				case 'ceil':
					$result = ceil($number / $nearest) * $nearest;
				break;

				case 'floor':
					$result = floor($number / $nearest) * $nearest;
				break;

				default:
					$result = round($number / $nearest) * $nearest;
				break;
			}
		}

		if ($negative === true)
		{
			$result = '-' . $result;
		}

		return $result;
	}

	private function trimToNearest($number, $nearest, $type = null)
	{
		if ($nearest > 1)
		{
			$multi = pow(10, ceil(log10($nearest)));
			$nearest = $nearest / $multi;
			$number = $number / $multi;
		}
		else
		{
			$multi = 1;
		}

		if ($nearest < 0.1)
		{
			$increment = 0.1;
		}
		else if ($nearest < 0.3)
		{
			$increment = 0.2;
		}
		else if ($nearest < 0.4)
		{
			$increment = 0.3;
		}
		else if ($nearest < 0.5)
		{
			$increment = 0.5;
		}
		else if ($nearest < 1)
		{
			$increment = 1;
		}

		if (0.3 == $increment)
		{
			$base = $number - $nearest;
			$pennies = $this->roundToNearest($base - floor($base), $increment, $type);
			$base = floor($base) + $pennies;
		}
		else
		{
			$base = $this->roundToNearest($number - $nearest, $increment, $type);
		}

		return ($base + $nearest) * $multi;
	}

	private function getRoundingRange($price)
	{
		if (!$price)
		{
			return null;
		}

		if (is_null($this->roundingRules))
		{
			$this->roundingRules = (array)unserialize($this->rounding->get());
			usort($this->roundingRules, array($this, 'sortRoundingRules'));
		}

		$price = abs($price);

		$l = count($this->roundingRules);
		for ($k = 0; $k < $l; $k++)
		{
			if ($this->roundingRules[$k]['range'] > $price)
			{
				return;
			}

			if (($this->roundingRules[$k]['range'] <= $price) && (!isset($this->roundingRules[$k + 1]) || ($this->roundingRules[$k + 1]['range'] > $price)))
			{
				return $this->roundingRules[$k];
			}
		}
	}

	private function sortRoundingRules($a, $b)
	{
		return $a['range'] - $b['range'];
	}
}

?>
