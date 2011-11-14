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

ClassLoader::import('library.shipping.ShippingRateSet');
ClassLoader::import('library.shipping.ShippingRateResult');
ClassLoader::import('application.model.delivery.ShippingService');

/**
 * Shipping cost calculation result for a particular Shipment. One Shipment can have several
 * ShipmentDeliveryRates - one for each available shipping service. Customer is able to choose between
 * the available rates. ShipmentDeliveryRate can be either a pre-defined rate or a real-time rate.
 *
 * @package application.model.delivery
 * @author Integry Systems <http://integry.com>
 */
class ShipmentDeliveryRate extends ShippingRateResult implements Serializable
{
	protected $amountWithTax;
	protected $amountWithoutTax;

	/**
	 * @var LiveCart
	 */
	private $application;

	private $service;

	public function setApplication($application)
	{
		$this->application = $application;
	}

	public static function getNewInstance(ShippingService $service, $cost)
	{
		$inst = new ShipmentDeliveryRate();
		$inst->setServiceId($service->getID());
		$inst->setApplication($service->getApplication());
		$inst->setCost((string)round($cost, 3), $service->getApplication()->getDefaultCurrencyCode());
		$inst->setService($service);
		$inst->setServiceName($service->getValueByLang('name'));
		return $inst;
	}

	public static function getRealTimeRates(ShippingRateCalculator $handler, Shipment $shipment)
	{
		$rates = new ShippingRateSet();

		$handler->setWeight($shipment->getChargeableWeight());

		$order = $shipment->order->get();
		if ($order->isMultiAddress->get())
		{
			$address = $shipment->shippingAddress->get();
		}
		else
		{
			$address = $order->shippingAddress->get();
		}

		if (!$address)
		{
			return $rates;
		}

		$handler->setDestCountry($address->countryID->get());
		$handler->setDestState($address->state->get() ? $address->state->get()->code->get() : $address->stateName->get());
		$handler->setDestZip($address->postalCode->get());
		$config = $shipment->getApplication()->getConfig();
		$handler->setSourceCountry($config->get('STORE_COUNTRY'));
		$handler->setSourceZip($config->get('STORE_ZIP'));
		$handler->setSourceState($config->get('STORE_STATE'));


		foreach ($handler->getAllRates() as $k => $rate)
		{
			$newRate = new ShipmentDeliveryRate();
			$newRate->setApplication($shipment->getApplication());
			$newRate->setCost($rate->getCostAmount(), $rate->getCostCurrency());
			$newRate->setServiceName($rate->getServiceName());
			$newRate->setClassName($rate->getClassName());
			$newRate->setProviderName($rate->getProviderName());
			$newRate->setServiceId($rate->getClassName() . '_' . $k);
			$rates->add($newRate);
		}

		return $rates;
	}

	public function getAmountByCurrency(Currency $currency)
	{
		$amountCurrency = Currency::getInstanceById($this->getCostCurrency());
		$amount = $currency->convertAmount($amountCurrency, $this->getCostAmount());

		return $amount;
	}

	public function setAmountByCurrency(Currency $currency, $amount)
	{
		$amountCurrency = Currency::getInstanceById($this->getCostCurrency());
		$this->setCost($amountCurrency->convertAmount($currency, $amount));
	}

	public function setAmountWithTax($amount)
	{
		$this->amountWithTax = (string)round($amount, 3);
	}

	public function setAmountWithoutTax($amount)
	{
		$this->amountWithoutTax = (string)round($amount, 3);
	}

	public function setService(ShippingService $service)
	{
		$this->service = $service;
	}

	public function getService()
	{
		return $this->service;
	}

	public function toArray($amount = null)
	{
		$array = parent::toArray();

		if (!is_null($amount))
		{
			$array['costAmount'] = $amount;
		}

		if (!$this->application)
		{
			$this->application = ActiveRecordModel::getApplication();
		}

		$amountCurrency = Currency::getInstanceById($array['costCurrency']);
		$currencies = $this->application->getCurrencySet();

		// get and format prices
		$prices = $formattedPrices = $taxPrices = $unformattedTaxPrices = array();

		foreach ($currencies as $id => $currency)
		{
			$prices[$id] = $currency->convertAmount($amountCurrency, $array['costAmount']);
			$formattedPrices[$id] = $currency->getFormattedPrice($prices[$id]);
			$unformattedTaxPrices[$id] = $currency->convertAmount($amountCurrency, $this->amountWithTax);
			$taxPrices[$id] = $currency->getFormattedPrice($unformattedTaxPrices[$id]);
			$withoutTaxPrices[$id] = $currency->convertAmount($amountCurrency, $this->amountWithoutTax);
			$formattedWithoutTaxPrices[$id] = $currency->getFormattedPrice($withoutTaxPrices[$id]);
		}

		$array['price'] = $prices;
		$array['priceWithTax'] = $unformattedTaxPrices;
		$array['formattedPrice'] = $formattedPrices;
		$array['taxPrice'] = $taxPrices;
		$array['priceWithoutTax'] = $withoutTaxPrices;
		$array['formattedPriceWithoutTax'] = $formattedWithoutTaxPrices;

		// shipping service name
		$id = $this->getServiceID();
		if (is_numeric($id))
		{
			try
			{
				$service = ShippingService::getInstanceById($id, ShippingService::LOAD_DATA);
				$array['ShippingService'] = $service->toArray();
			}
			catch (ARNotFoundException $e)
			{
				return array();
			}
		}
		else
		{
			$array['ShippingService'] = array('name_lang' => $this->getServiceName(), 'provider' => $this->getProviderName());
		}

		return $array;
	}

	public function serialize()
	{
		$vars = get_object_vars($this);
		unset($vars['application']);
		unset($vars['service']);

		return serialize($vars);
	}

	public function unserialize($serialized)
	{
		foreach (unserialize($serialized) as $key => $value)
		{
			$this->$key = $value;
		}
	}
}
?>
