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

ClassLoader::import("application.controller.backend.abstract.StoreManagementController");
ClassLoader::import("application.model.order.*");
ClassLoader::import("application.model.delivery.*");
ClassLoader::import("application.model.Currency");
ClassLoader::import("application.model.product.ProductSet");

/**
 * Controller for handling product based actions performed by store administrators
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role order
 */
class ShipmentController extends StoreManagementController
{
	public function init()
	{
		parent::init();
		CustomerOrder::allowEmpty();
	}

	public function changeService()
	{
		$shipment = Shipment::getInstanceByID('Shipment', (int)$this->request->get('id'), true, array('Order' => 'CustomerOrder', 'ShippingAddress' => 'UserAddress'));
		$shipment->loadItems();
		$order = $shipment->order->get();
		$shipment->order->get()->loadAll();
		$zone = $shipment->getDeliveryZone();
		$shipmentRates = $zone->getShippingRates($shipment);

		$shipment->setAvailableRates($shipmentRates);

		$history = new OrderHistory($order, $this->user);

		$selectedRate = null;
		foreach($shipment->getAvailableRates() as $rate)
		{
			if($rate->getServiceID() == $this->request->get('serviceID'))
			{
				$selectedRate = $rate;
				break;
			}
		}

		$shipment->setRateId($this->request->get('serviceID'));

		$shipment->recalculateAmounts();
		$order->save();
		$shipment->save(ActiveRecord::PERFORM_UPDATE);

		$history->saveLog();

		$shipmentArray = $shipment->toArray();
		$shipmentArray['ShippingService']['ID'] = $this->request->get('serviceID');

		return new JSONResponse(array(
				'shipment' => array(
					   'ID' => $shipment->getID(),
					   'amount' => $shipment->amount->get(),
					   'shippingAmount' => (float)$shipment->shippingAmount->get(),
					   'taxAmount' => $shipment->taxAmount->get(),
					   'total' => $shipment->shippingAmount->get() + $shipment->amount->get() + (float)$shipment->taxAmount->get(),
					   'prefix' => $shipment->getCurrency()->pricePrefix->get(),
					   'suffix' => $shipment->getCurrency()->priceSuffix->get(),
					   'ShippingService' => $shipmentArray['ShippingService'],
					   'Order' => $shipment->order->get()->toFlatArray(),
				   )
			),
			'success'
		);
	}

	public function changeStatus()
	{
		$status = (int)$this->request->get('status');

		$shipment = Shipment::getInstanceByID('Shipment', (int)$this->request->get('id'), true, array('Order' => 'CustomerOrder', 'ShippingAddress' => 'UserAddress'));
		$shipment->loadItems();

		$zone = $shipment->getDeliveryZone();
		$shipmentRates = $zone->getShippingRates($shipment);
		$shipment->setAvailableRates($shipmentRates);

		$history = new OrderHistory($shipment->order->get(), $this->user);

		$shipment->status->set($status);
		$shipment->save();

		$history->saveLog();

		$status = $shipment->status->get();
		$enabledStatuses = $this->config->get('EMAIL_STATUS_UPDATE_STATUSES');
		$m = array(
			'EMAIL_STATUS_UPDATE_NEW'=>Shipment::STATUS_NEW,
			'EMAIL_STATUS_UPDATE_PROCESSING'=>Shipment::STATUS_PROCESSING,
			'EMAIL_STATUS_UPDATE_AWAITING_SHIPMENT'=>Shipment::STATUS_AWAITING,
			'EMAIL_STATUS_UPDATE_SHIPPED'=> Shipment::STATUS_SHIPPED
		);
		$sendEmail = false;
		foreach($m as $configKey => $constValue)
		{
			if($status == $constValue && array_key_exists($configKey, $enabledStatuses))
			{
				$sendEmail = true;
			}
		}

		if ($sendEmail || $this->config->get('EMAIL_STATUS_UPDATE'))
		{
			$user = $shipment->order->get()->user->get();
			$user->load();

			$email = new Email($this->application);
			$email->setUser($user);
			$email->setTemplate('order.status');
			$email->set('order', $shipment->order->get()->toArray(array('payments' => true)));
			$email->set('shipments', array($shipment->toArray()));
			$email->send();
		}

		return new JSONResponse(false, 'success');
	}

	public function getAvailableServices()
	{
		$this->loadLanguageFile('Checkout');

		if($shipmentID = (int)$this->request->get('id'))
		{
			$shipment = Shipment::getInstanceByID('Shipment', $shipmentID, true, array('Order' => 'CustomerOrder'));
			$shipment->loadItems();

			if ($shipment->shippingAddress->get())
			{
				$shipment->shippingAddress->get()->load();
			}

			$zone = $shipment->getDeliveryZone();

			$shipmentRates = $zone->getShippingRates($shipment);
			$shipment->setAvailableRates($shipmentRates);

			$shippingRatesArray = array();
			foreach($shipment->getAvailableRates() as $rate)
			{
				$rateArray = $rate->toArray();
				$shippingRatesArray[$rateArray['serviceID']] = $rateArray;
				$shippingRatesArray[$rateArray['serviceID']]['shipment'] = array(
					'ID' => $shipment->getID(),
					'amount' => $shipment->amount->get(),
					'shippingAmount' => (float)$rateArray['costAmount'],
					'taxAmount' => $shipment->taxAmount->get(),
					'total' => (float)$shipment->taxAmount->get() + (float)$shipment->amount->get() + (float)$rateArray['costAmount'],
					'prefix' => $shipment->getCurrency()->pricePrefix->get(),
					'suffix' => $shipment->getCurrency()->priceSuffix->get()
				);
			}

			return new JSONResponse(array( 'services' => $shippingRatesArray));
		}
	}

	private function createShipmentFormValidator()
	{
		$validator = $this->getValidator('shippingService', $this->request);

		return $validator;
	}

	/**
	 * @role update
	 */
	public function create()
	{
		$order = CustomerOrder::getInstanceByID((int)$this->request->get('orderID'), true, array('BillingAddress', 'ShippingAddress'));

		$shipment = Shipment::getNewInstance($order);
		$history = new OrderHistory($order, $this->user);
		$response = $this->save($shipment);
		$history->saveLog();

		return $response;
	}

	public function editAddress()
	{
		$this->loadLanguageFile('backend/CustomerOrder');

		ClassLoader::import('application.controller.backend.CustomerOrderController');
		$shipment = Shipment::getInstanceByID('Shipment', $this->request->get('id'), true, array('CustomerOrder', 'User'));

		if (!$shipment->shippingAddress->get())
		{
			$shipment->shippingAddress->set(UserAddress::getNewInstance());
			$shipment->shippingAddress->get()->save();
		}

		$shipment->shippingAddress->get()->load();
		$address = $shipment->shippingAddress->get()->toArray();

		$response = new ActionResponse();
		$controller = new CustomerOrderController($this->application);
		$response->set('form', $controller->createUserAddressForm($address, $response));

		$response->set('countries', $this->application->getEnabledCountries());
		$response->set('states', State::getStatesByCountry($address['countryID']));
		$response->set('shipmentID', $shipment->getID());

		$addressOptions = array('' => '');
		$addresses = array();
		foreach(array_merge($shipment->order->get()->user->get()->getShippingAddressArray(), $shipment->order->get()->user->get()->getBillingAddressArray()) as $address)
		{
			$addressOptions[$address['ID']] = $address['UserAddress']['compact'];
			$addresses[$address['ID']] = $address;
		}
		$response->set('existingUserAddressOptions', $addressOptions);
		$response->set('existingUserAddresses', $addresses);

		return $response;
	}

	public function saveAddress()
	{
		$this->loadLanguageFile('backend/Shipment');

		ClassLoader::import('application.controller.backend.CustomerOrderController');
		$shipment = Shipment::getInstanceByID('Shipment', $this->request->get('id'), true, array('CustomerOrder', 'User'));
		$address = $shipment->shippingAddress->get();

		if (!$address)
		{
			$address = UserAddress::getNewInstance();
			$address->save();
			$shipment->shippingAddress->set($address);
			$shipment->save();
		}
		else
		{
			$address->load();
		}

		$controller = new CustomerOrderController($this->application);
		$validator = $controller->createUserAddressFormValidator();

		if ($validator->isValid())
		{
			$address->loadRequestData($this->request);
			$address->save();
			return new JSONResponse($shipment->shippingAddress->get()->toArray(), 'success', $this->translate('_shipment_address_changed'));
		}
		else
		{
			return new JSONResponse(
				array(
					'errors' => $validator->getErrorList()
				),
				'failure'
			);
		}
	}

	/**
	 * @role update
	 */
	public function update()
	{
		$order = CustomerOrder::getInstanceByID((int)$this->request->get('ID'));
		return $this->save($order);
	}

	/**
	 * @role update
	 */
	public function updateShippingAmount()
	{
		$shipment = Shipment::getInstanceByID('Shipment', $this->request->get('id'), true, array('CustomerOrder', 'User'));
		$order = $shipment->order->get();

		$order->loadAll();

		$shipment->shippingAmount->set($shipment->reduceTaxesFromShippingAmount($this->request->get('amount')));
		$shipment->recalculateAmounts(true);
		$shipment->save();

		$order->getTotal(true);
		$order->save();

		$array = $shipment->toArray();
		$array['total'] = $order->getTotal();

		unset($array['items']);
		unset($array['taxes']);

		$array['Order'] = $order->toFlatArray();

		return new JSONResponse(array('Shipment' => $array));
	}

	private function save(Shipment $shipment)
	{
		$validator = $this->createShipmentFormValidator();
		if ($validator->isValid())
		{
			if($shippingServiceID = $this->request->get('shippingServiceID'))
			{
				$shippingService = ShippingService::getInstanceByID($shippingServiceID);

				$shipment->shippingService->set($shippingService);
				$shipment->setAvailableRates($shipment->getDeliveryZone()->getShippingRates($shipment));
				$shipment->setRateId($shippingService->getID());
			}

			if($this->request->get('noStatus'))
			{
				$shipment->status->set($shipment->order->get()->status->get());
			}
			else if($this->request->get('shippingServiceID') || ((int)$this->request->get('status') < 3))
			{
				$shipment->status->set((int)$this->request->get('status'));
			}

			$shipment->save();

			return new JSONResponse(
				array(
					'shipment' => array(
						'ID' => $shipment->getID(),
						'amount' => $shipment->amount->get(),
						'shippingAmount' => $shipment->shippingAmount->get(),
						'ShippingService' => array('ID' => ($shipment->shippingService->get() ? $shipment->shippingService->get()->getID() : 0) ),
						'taxAmount' => $shipment->taxAmount->get(),
						'total' => $shipment->shippingAmount->get() + $shipment->amount->get() + (float)$shipment->taxAmount->get(),
						'prefix' => $shipment->getCurrency()->pricePrefix->get(),
						'status' => $shipment->status->get(),
						'suffix' => $shipment->getCurrency()->priceSuffix->get()
					)
				),
				'success',
				($this->request->get('noStatus') ? false : $this->translate('_new_shipment_has_been_successfully_created'))
			);
		}
		else
		{
			return new JSONResponse(
				array(
					'errors' => $validator->getErrorList()
				),
				'failure',
				$this->translate('_error_creating_new_shipment')
			);
		}
	}

	public function edit()
	{
		$group = ProductFileGroup::getInstanceByID((int)$this->request->get('id'), true);

		return new JSONResponse($group->toArray());
	}

	/**
	 * @role update
	 */
	public function delete()
	{
		$shipment = Shipment::getInstanceByID('Shipment', (int)$this->request->get('id'), true, array('Order' => 'CustomerOrder'));
		$shipment->order->get()->loadAll();

		$history = new OrderHistory($shipment->order->get(), $this->user);

		$shipment->delete();

		$shipment->order->get()->updateStatusFromShipments();
		$shipment->order->get()->save();

		$history->saveLog();

		return new JSONResponse(array('deleted' => true), 'success');
	}

	protected function getDownloadCounts($itemIDs)
	{
		if (!$itemIDs)
		{
			return array();
		}

		$sql = 'SELECT orderedItemID, SUM(timesDownloaded) AS cnt FROM OrderedFile WHERE orderedItemID IN (' . implode(',', $itemIDs) . ') GROUP BY orderedItemID';
		$out = array();
		foreach (ActiveRecordModel::getDataBySQL($sql) as $item)
		{
			$out[$item['orderedItemID']] = $item['cnt'];
		}

		return $out;
	}
}

?>
