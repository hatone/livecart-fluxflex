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

ClassLoader::import('application.model.delivery.*');
ClassLoader::import('application.model.tax.TaxRate');
ClassLoader::import('library.shipping.ShippingRateSet');

/**
 * Delivery zones are used to classify shipping locations, which allows to define different
 * shipping rates and taxes for different delivery addresses.
 *
 * Delivery zone is determined automatically when user proceeds with the checkout and enters
 * the shipping address. In case no rules match for the shipping zone, the default delivery
 * zone is used.
 *
 * The delivery zone address rules can be set up in several ways - by assigning whole countries
 * or states or by defining mask strings, that allow to recognize addresses by city names, postal
 * codes or even street addresses.
 *
 * @package application.model.delivery
 * @author Integry Systems <http://integry.com>
 */
class DeliveryZone extends MultilingualObject
{
	const BOTH_RATES = 0;
	const TAX_RATES = 1;
	const SHIPPING_RATES = 2;

	const ENABLED_TAXES = false;

	private $taxRates = null;

	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName("DeliveryZone");

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARField("name", ARText::instance()));
		$schema->registerField(new ARField("isEnabled", ARInteger::instance(1)));
		$schema->registerField(new ARField("isFreeShipping", ARInteger::instance(1)));
		$schema->registerField(new ARField("isRealTimeDisabled", ARInteger::instance(1)));
		$schema->registerField(new ARField("type", ARInteger::instance(1)));
	}

	/*####################  Static method implementations ####################*/

	/**
	 * @return DeliveryZone
	 */
	public static function getNewInstance()
	{
	  	return ActiveRecord::getNewInstance(__CLASS__);
	}

	/**
	 * Gets an existing record instance (persisted on a database).
	 * @param mixed $recordID
	 *
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * @return DeliveryZone
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{
		if ((0 == $recordID) && $loadRecordData)
		{
			return self::getDefaultZoneInstance();
		}
		else
		{
			return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords, $data);
		}
	}

	public function isDefault()
	{
		return 0 == $this->getID();
	}

	public function isTaxIncludedInPrice()
	{
		return $this->isDefault() || $this->hasSameTaxRatesAsDefaultZone();
	}

	public function hasSameTaxRatesAsDefaultZone()
	{
		$defaultZoneRates = self::getDefaultZoneInstance()->getTaxRates();
		$ownRates = $this->getTaxRates();

		if (!$ownRates->size() || !$defaultZoneRates->size())
		{
			return false;
		}

		foreach ($ownRates as $rate)
		{
			foreach ($defaultZoneRates as $dzRate)
			{
				$found = false;
				if (($dzRate->tax->get() == $rate->tax->get()) && ($dzRate->taxClass->get() == $rate->taxClass->get()))
				{
					$found = true;
					if ($dzRate->rate->get() != $rate->rate->get())
					{
						return false;
					}
				}

				if (!$found)
				{
					return false;
				}
			}
		}

		return true;
	}

	/*####################  Instance retrieval ####################*/

	/**
	 * @return ARSet
	 */
	public static function getAll($type = null)
	{
		$filter = new ARSelectFilter();

		if ($type)
		{
			$filter = new ARSelectFilter(new InCond(new ARFieldHandle('DeliveryZone','type'), array(DeliveryZone::BOTH_RATES, $type)));
		}

		return self::getRecordSet(__CLASS__, $filter);
	}

	/**
	 * @return ARSet
	 */
	public static function getEnabled()
	{
		$filter = new ARSelectFilter();
		$filter->setCondition(new EqualsCond(new ArFieldHandle(__CLASS__, "isEnabled"), 1));

		return self::getRecordSet(__CLASS__, $filter);
	}

	/**
	 * Returns the delivery zone, which matches the required address
	 *
	 * @return DeliveryZone
	 * @todo implement
	 */
	public static function getZoneByAddress(UserAddress $address, $type = 0)
	{
		$zones = self::getAllZonesByAddress($address, $type);
		return $zones ? array_shift($zones) : DeliveryZone::getDefaultZoneInstance();
	}

	public static function getTaxZones()
	{
		return self::getAll(self::TAX_RATES);
	}

	public static function getAllZonesByAddress(UserAddress $address, $type = 0)
	{
		if (!$address->isLoaded())
		{
			$address->load();
		}
		$zones = array();
		// get zones by state
		if ($address->state->get())
		{
			$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle('DeliveryZone', 'isEnabled'), true));
			$f->mergeCondition(new EqualsCond(new ARFieldHandle('DeliveryZoneState', 'stateID'), $address->state->get()->getID()));
			$f->mergeCondition(new InCond(new ARFieldHandle('DeliveryZone','type'), array(DeliveryZone::BOTH_RATES, $type)));
			$s = ActiveRecordModel::getRecordSet('DeliveryZoneState', $f, ActiveRecordModel::LOAD_REFERENCES);
			foreach ($s as $zoneState)
			{
				$zones[] = $zoneState->deliveryZone->get();
			}
		}

		// get zones by country
		if (!$zones)
		{
			$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle('DeliveryZone', 'isEnabled'), true));
			$f->mergeCondition(new EqualsCond(new ARFieldHandle('DeliveryZoneCountry', 'countryCode'), $address->countryID->get()));
			$f->mergeCondition(new InCond(new ARFieldHandle('DeliveryZone','type'), array(DeliveryZone::BOTH_RATES, $type)));
			$s = ActiveRecordModel::getRecordSet('DeliveryZoneCountry', $f, array('DeliveryZone'));
			foreach ($s as $zone)
			{
				$zones[] = $zone->deliveryZone->get();
			}
		}

		$maskPoints = array();

		// leave zones that match masks
		foreach ($zones as $key => $zone)
		{
			$match = $zone->getMaskMatch($address);

			if (!$match)
			{
				unset($zones[$key]);
			}
			else
			{
				$maskPoints[$key] = $match;
			}
		}

		if ($maskPoints)
		{
			arsort($maskPoints);
			// this should really be a one-liner, but not today
			$ret = array();
			foreach (array_keys($maskPoints) as $key)
			{
				$ret[] = $zones[$key];
			}

			return $ret;
		}

		return $zones;
	}

	/**
	 * Returns the default delivery zone instance
	 *
	 * @return DeliveryZone
	 */
	public static function getDefaultZoneInstance()
	{
		return self::getInstanceById(0);
	}

	/*####################  Get related objects ####################*/

	/**
	 * Determine if the supplied UserAddress matches address masks
	 *
	 * @return bool
	 */
	public function getMaskMatch(UserAddress $address)
	{
		return
			   $this->hasMaskGroupMatch($this->getCityMasks(), $address->city->get()) +

			   ($this->hasMaskGroupMatch($this->getAddressMasks(), $address->address1->get()) ||
			   $this->hasMaskGroupMatch($this->getAddressMasks(), $address->address2->get())) +

			   $this->hasMaskGroupMatch($this->getZipMasks(), $address->postalCode->get());
	}

	private function hasMaskGroupMatch(ARSet $masks, $addressString)
	{
		if (!$masks->size())
		{
			return true;
		}

		$match = false;

		foreach ($masks as $mask)
		{
			if ($this->isMaskMatch($addressString, $mask->mask->get()))
			{
				$match = 2;
			}
		}

		return $match;
	}

	private function isMaskMatch($addressString, $maskString)
	{
		$maskString = str_replace('*', '.*', $maskString);
		$maskString = str_replace('?', '.{0,1}', $maskString);
		$maskString = str_replace('/', '\/', $maskString);
		$maskString = str_replace('\\', '\\\\', $maskString);
		return @preg_match('/' . $maskString . '/im', $addressString);
	}

	/**
	 *  Returns manually defined shipping rates for the particular shipment
	 *
	 *	@return ShippingRateSet
	 */
	public function getDefinedShippingRates(Shipment $shipment)
	{
		$rates = new ShippingRateSet();
		foreach ($this->getShippingServices() as $service)
		{
			$rate = $service->getDeliveryRate($shipment);
			if ($rate)
			{
				$rates->add($rate);
			}
		}

		if (!$shipment->getChargeableItemCount($this))
		{
			$app = self::getApplication();

			if ($app->getConfig()->get('FREE_SHIPPING_AUTO_RATE'))
			{
				$freeService = ShippingService::getNewInstance($this, $app->translate('_free_shipping'), ShippingService::WEIGHT_BASED);
				$freeRate = ShipmentDeliveryRate::getNewInstance($freeService, 0);
				$freeRate->setServiceID('FREE');
				$rates->add($freeRate);
			}
		}

		return $rates;
	}

	/**
	 *  Returns real time shipping rates for the particular shipment
	 *
	 *	@return ShippingRateSet
	 */
	public function getRealTimeRates(Shipment $shipment)
	{
		$rates = new ShippingRateSet();

		$app = self::getApplication();
		foreach ($app->getEnabledRealTimeShippingServices() as $handler)
		{
			$rates->merge(ShipmentDeliveryRate::getRealTimeRates($app->getShippingHandler($handler), $shipment));
		}

		return $rates;
	}

	/**
	 *  Returns both real time and calculated shipping rates for the particular shipment
	 *
	 *	@return ShippingRateSet
	 */
	public function getShippingRates(Shipment $shipment)
	{
		$defined = $this->getDefinedShippingRates($shipment);
		if (!$this->isRealTimeDisabled->get())
		{
			$defined->merge($this->getRealTimeRates($shipment));
		}

		// calculate surcharge
		$surcharge = 0;
		foreach ($shipment->getItems() as $item)
		{
			$surcharge += ($item->getProduct()->getParent()->shippingSurchargeAmount->get() * $item->getCount());
		}

		$currency = self::getApplication()->getDefaultCurrency();

		// apply to rates
		foreach ($defined as $rate)
		{
			$rate->setAmountByCurrency($currency, $rate->getAmountByCurrency($currency) + $surcharge);
		}

		// apply taxes
		foreach ($defined as $rate)
		{
			$zone = $shipment->getShippingTaxZone();
			$amount = !$zone->isDefault() ? $shipment->applyTaxesToShippingAmount($rate->getCostAmount()) : $rate->getCostAmount();
			$rate->setAmountWithTax($amount);
			$rate->setAmountWithoutTax($shipment->reduceTaxesFromShippingAmount($amount));
		}

		// look for "override" rates
		foreach ($defined as $rate)
		{
			if ($service = $rate->getService())
			{
				if ($service->isFinal->get())
				{
					$rates = new ShippingRateSet();
					$rates->add($rate);
					return $rates;
				}
			}
		}

		return $defined;
	}

	/**
	 *
	 *	@return ARSet
	 */
	public function getTaxRates($includeDisabled = true, $loadReferencedRecords = array('Tax'))
	{
		if (!$this->taxRates)
		{
			$this->taxRates = TaxRate::getRecordSetByDeliveryZone($this, $loadReferencedRecords);
		}

		return $this->taxRates;
	}

	/**
	 * @return ARSet
	 */
	public function getCountries($loadReferencedRecords = false)
	{
		return DeliveryZoneCountry::getRecordSetByZone($this, $loadReferencedRecords);
	}

	/**
	 * @return ARSet
	 */
	public function getStates($loadReferencedRecords = array('State'))
	{
		return DeliveryZoneState::getRecordSetByZone($this, $loadReferencedRecords);
	}

	/**
	 * @return ARSet
	 */
	public function getCityMasks($loadReferencedRecords = false)
	{
		return DeliveryZoneCityMask::getRecordSetByZone($this, $loadReferencedRecords);
	}

	/**
	 * @return ARSet
	 */
	public function getZipMasks($loadReferencedRecords = false)
	{
		return DeliveryZoneZipMask::getRecordSetByZone($this, $loadReferencedRecords);
	}

	/**
	 * @return ARSet
	 */
	public function getAddressMasks($loadReferencedRecords = false)
	{
		return DeliveryZoneAddressMask::getRecordSetByZone($this, $loadReferencedRecords);
	}


	/**
	 * Get set of shipping sevices available in current zone
	 *
	 * @param boolean $loadReferencedRecords
	 * @return ARSet
	 */
	public function getShippingServices($loadReferencedRecords = false)
	{
		return ShippingService::getByDeliveryZone($this, $loadReferencedRecords);
	}
}

?>
