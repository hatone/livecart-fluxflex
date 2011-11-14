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
ClassLoader::import('application.controller.backend.abstract.StoreManagementController');
ClassLoader::import('application.controller.OrderController');
ClassLoader::import("application.model.order.*");
ClassLoader::import("application.model.Currency");
ClassLoader::import("application.model.product.*");
ClassLoader::import("library.*");

/**
 * ...
 *
 * @package application.controller.backend
 * @author Integry Systems
 *
 * @role order
 */
class OrderedItemController extends StoreManagementController
{
	public function init()
	{
		parent::init();
		CustomerOrder::allowEmpty();
	}

	public function create()
	{
		$request = $this->getRequest();
		$query = $request->get('query');

		if (strlen($query))
		{
			$products = $this->getProductsFromSearchQuery($query);
		}
		else
		{
			$products = new ARSet();
			$products->add(Product::getInstanceById((int)$this->request->get('productID'), true));
		}

		$saveResponse = array('errors'=>array(), 'items'=>array());

		$composite = new CompositeJSONResponse();


		$order = CustomerOrder::getInstanceByID((int)$this->request->get('orderID'), true);
		$order->loadAll();

		foreach ($products as $product)
		{
			if($product->isDownloadable())
			{
				$shipment = $order->getDownloadShipment();
			}
			else if ((int)$this->request->get('shipmentID'))
			{
				$shipment = Shipment::getInstanceById('Shipment', (int)$this->request->get('shipmentID'), true, array('Order' => 'CustomerOrder', 'ShippingService', 'ShippingAddress' => 'UserAddress', 'Currency'));
			}

			if (empty($shipment))
			{
				$shipment = $order->getShipments()->get(0);
			}

			if (!$shipment)
			{
				$shipment = Shipment::getNewInstance($order);
			}

			if (!$shipment->order->get())
			{
				$shipment->order->set($order);
			}

			$history = new OrderHistory($order, $this->user);

			$existingItem = false;
			foreach($shipment->getItems() as $item)
			{
				if($item->getProduct() === $product)
				{
					if (!$product->getOptions(true))
					{
						$existingItem = $item;
					}
					break;
				}
			}

			if($existingItem)
			{
				$item = $existingItem;
				if($product->isDownloadable())
				{
					return new JSONResponse(false, 'failure', $this->translate('_downloadable_item_already_exists_in_this_order'));
				}
				else
				{
					$item->count->set($item->count->get() + 1);
				}
			}
			else
			{
				$currency = $shipment->getCurrency();
				$item = OrderedItem::getNewInstance($order, $product);
				$item->count->set(1);
				$item->price->set($currency->round($item->reduceBaseTaxes($product->getPrice($currency->getID()))));
				$order->addItem($item);
				$shipment->addItem($item);
				$shipment->save();
			}

			$resp = $this->save($item, $shipment, $existingItem ? true : false );

			if (array_key_exists('errors', $resp))
			{
				$saveResponse['errors'] = array_merge($saveResponse['errors'], $resp['errors']);
			}
			else if(array_key_exists('item', $resp))
			{
				$saveResponse['items'][] = $resp['item'];
			}
		} // for each product

		if (count($saveResponse['errors']) == 0)
		{
			unset($saveResponse['errors']);
		}

		if (isset($saveResponse['errors']))
		{
			$response =  new JSONResponse(array('errors' => $validator->getErrorList()), 'failure', $this->translate('_unable_to_update_items_quantity'));
		}
		else
		{
			$response = new JSONResponse($saveResponse, 'success', $this->translate('_item_has_been_successfuly_saved'));
		}
		$composite->addResponse('data', $response, $this, 'create');

		$ids = array();
		foreach($saveResponse['items'] as $item)
		{
			$ids[] = $item['ID'];
		}

		$composite->addAction('html', 'backend.orderedItem', 'items');
		$this->request->set('item_ids', implode(',',$ids));

		$history->saveLog();

		return $composite;
	}

	private function getProductsFromSearchQuery($query)
	{
		ClassLoader::import('application.model.searchable.SearchableModel');
		$request = $this->getRequest();
		$searchable  = SearchableModel::getInstanceByModelClass('Product',SearchableModel::BACKEND_SEARCH_MODEL);
		$searchable->setOption('BACKEND_QUICK_SEARCH', true);

		return ActiveRecordModel::getRecordSet('Product', $searchable->getSelectFilter($query));
	}

	public function update()
	{

	}

	private function save(OrderedItem $item, Shipment $shipment, $existingItem = false)
	{
		$validator = $this->createOrderedItemValidator();
		if($validator->isValid())
		{
			if($count = (int)$this->request->get('count') && !(int)$this->request->get('downloadable'))
			{
				$item->count->set($count);
			}

			$shipment->loadItems();

			if(!$existingItem)
			{
				$shipment->addItem($item);
			}

			if($shipment->getShippingService())
			{
				$shipmentRates = $shipment->getDeliveryZone()->getShippingRates($shipment);
				$shipment->setAvailableRates($shipmentRates);
				$shipment->setRateId($shipment->getShippingService()->getID());
			}

			$shipment->recalculateAmounts();
			$shipment->save();

			return array(
				'item' => array(
					'ID' => $item->getID(),
					'Product' => $item->getProduct()->toArray(),
					'Shipment' => array(
						'ID' => $shipment->getID(),
						'amount' => (float)$shipment->amount->get(),
						'shippingAmount' => (float)$shipment->shippingAmount->get(),
						'taxAmount' => (float)$shipment->taxAmount->get(),
						'total' => (float)$shipment->shippingAmount->get() + (float)$shipment->amount->get() + (float)$shipment->taxAmount->get(),
						'prefix' => $shipment->getCurrency()->pricePrefix->get(),
						'suffix' => $shipment->getCurrency()->priceSuffix->get(),
						'Order' => $shipment->order->get()->toFlatArray(),
					),
					'count' => $item->count->get(),
					'price' => $item->price->get(),
					'priceCurrencyID' => $item->getCurrency()->getID(),
					'isExisting' => $existingItem,
					'variations' => $item->getProduct()->getParent()->getVariationData($this->application),
				)
			);
		}
		else
		{
			return array(
				'errors' => $validator->getErrorList()
			);

			//return new JSONResponse(array('errors' => $validator->getErrorList()), 'failure', $this->translate('_unable_to_update_items_quantity'));
		}
	}

	/**
	 * Products popup
	 *
	 * @role update
	 */
	public function selectProduct()
	{
		ClassLoader::import("application.model.category.Category");

		$response = new ActionResponse();

		$categoryList = Category::getRootNode()->getDirectChildNodes();
		$categoryList->unshift(Category::getRootNode());

		$response->set("categoryList", $categoryList->toArray($this->application->getDefaultLanguageCode()));

		$order = CustomerOrder::getInstanceById($this->request->get('id'), true, true);

		$response->set("order", $order->toFlatArray());
		$response->set("shipments", $this->getOrderShipments($order));

		return $response;
	}

	public function shipments()
	{
		$order = CustomerOrder::getInstanceById($this->request->get('id'), true, true);
		return new ActionResponse("shipments", $this->getOrderShipments($order));
	}

	private function getOrderShipments(CustomerOrder $order)
	{
		$order->loadItems();
		$shipments = $order->getShipments();
		$downloadable = $order->getDownloadShipment(false);

		if ($downloadable && count($shipments) == 1 && !count($downloadable->getItems()))
		{
			$downloadable = null;
		}

		$shipmentsArray = array();
		foreach($shipments as $key => $shipment)
		{
			// one shipment is reserved for downloadable items
			if ($shipment === $downloadable || $shipment->isShipped())
			{
				continue;
			}

			$shipmentsArray[$shipment->getID()] = $shipment->toArray();
			$rate = unserialize($shipment->shippingServiceData->get());

			if(is_object($rate))
			{
				$rate->setApplication($this->application);
				$shipmentsArray[$shipment->getID()] = array_merge($shipmentsArray[$shipment->getID()], $rate->toArray());
				$shipmentsArray[$shipment->getID()]['ShippingService']['ID'] = $shipmentsArray[$shipment->getID()]['serviceID'];
			}
			else
			{
				$shipmentsArray[$shipment->getID()]['ShippingService']['name_lang'] = $this->translate('_shipping_service_is_not_selected');
			}
		}

		return $shipmentsArray;
	}

	/**
	 * @return RequestValidator
	 */
	private function createOrderedItemValidator()
	{
		$validator = $this->getValidator('orderedItem', $this->request);
		$validator->addCheck('productID', new MinValueCheck('_err_invalid_product', 0));
		$validator->addCheck('orderID', new MinValueCheck('_err_invalid_customer_order', 0));
		$validator->addCheck('count', new MinValueCheck('_err_count_should_be_more_than_zero', 0));

		return $validator;
	}

	/**
	 * Delete filter from database
	 *
	 * @role update
	 *
	 * @return JSONResponse
	 */
	public function delete()
	{
		if($id = $this->request->get("id", false))
		{
			$item = OrderedItem::getInstanceByID('OrderedItem', (int)$id, true, array('Shipment', 'Order' => 'CustomerOrder', 'ShippingService', 'Currency', 'ShippingAddress' => 'UserAddress', 'Product'));
			$shipment = $item->shipment->get();
			$order = $shipment->order->get();
			$order->loadItems();

			$history = new OrderHistory($order, $this->user);

			$shipment->loadItems();
			$shipment->removeItem($item);
			$order->removeItem($item);

			if($shipment->getShippingService())
			{
				$shipmentRates = $shipment->getDeliveryZone()->getShippingRates($shipment);
				$shipment->setAvailableRates($shipmentRates);
				$shipment->setRateId($shipment->getShippingService()->getID());
			}

			$shipment->recalculateAmounts();
			$shipment->save();

			$history->saveLog();

			return new JSONResponse(array(
					'item' => array(
						'ID'			  => $item->getID(),
						'Shipment'		=> array(
												'ID' => $shipment->getID(),
												'amount' => $shipment->amount->get(),
												'shippingAmount' => $shipment->shippingAmount->get(),
												'taxAmount' => $shipment->taxAmount->get(),
												'total' =>((float)$shipment->shippingAmount->get() + (float)$shipment->amount->get() + (float)$shipment->taxAmount->get()),
												'prefix' => $shipment->getCurrency()->pricePrefix->get(),
												'suffix' => $shipment->getCurrency()->priceSuffix->get(),
												'Order' => $shipment->order->get()->toFlatArray()
											 ),
						'count'		   => $item->count->get(),
						'price'		   => $item->price->get(),
						'priceCurrencyID' => $item->getCurrency()->getID(),
						'downloadable' => $item->getProduct()->isDownloadable()
					)
				),
				'success'
			);
		}
		else
		{
			return new JSONResponse(false, 'failure', '_error_removing_item_from_shipment');
		}
	}

	/**
	 * @role update
	 */
	public function changeShipment()
	{
		if(($id = (int)$this->request->get("id", false)) && ($fromID = (int)$this->request->get("from", false)) && ($toID = (int)$this->request->get("to", false)))
		{
			$item = OrderedItem::getInstanceByID('OrderedItem', $id, true, array('Product'));

			$oldShipment = Shipment::getInstanceByID('Shipment', $fromID, true, array('Order' => 'CustomerOrder', 'ShippingAddress' => 'UserAddress', 'Currency'));
			$newShipment = Shipment::getInstanceByID('Shipment', $toID, true, array('Order' => 'CustomerOrder', 'ShippingAddress' => 'UserAddress', 'Currency'));

			$history = new OrderHistory($oldShipment->order->get(), $this->user);

			$zone = $oldShipment->getDeliveryZone();

			if($oldShipment !== $newShipment)
			{
				$oldShipment->loadItems();
				$oldShipment->removeItem($item);

				$newShipment->loadItems();
				$newShipment->addItem($item);


				if($oldShipment->getShippingService())
				{
					$shipmentRates = $zone->getShippingRates($oldShipment);
					$oldShipment->setAvailableRates($shipmentRates);

					$oldShipment->setRateId($oldShipment->getShippingService()->getID());
					$oldShipment->save();
				}

				if($newShipment->getShippingService())
				{
					$shipmentRates = $zone->getShippingRates($newShipment);
					$newShipment->setAvailableRates($shipmentRates);

					$newShipment->setRateId($newShipment->getShippingService()->getID());
					$newShipment->save();
				}

				$item->save();

				if($newShipment->getSelectedRate() || !$newShipment->getShippingService() || !is_int($newShipment->getShippingService()->getID()))
				{
					$item->save();

					$oldShipment->recalculateAmounts();
					$newShipment->recalculateAmounts();

					$oldShipment->save();
					$newShipment->save();

					$history->saveLog();

					return new JSONResponse(
						array(
							'oldShipment' => array(
								'ID' => $oldShipment->getID(),
								'amount' => $oldShipment->amount->get(),
								'shippingAmount' => $oldShipment->shippingAmount->get(),
								'taxAmount' => $oldShipment->taxAmount->get(),
								'total' =>((float)$oldShipment->shippingAmount->get() + (float)$oldShipment->amount->get() + (float)$oldShipment->taxAmount->get()),
								'prefix' => $oldShipment->getCurrency()->pricePrefix->get(),
								'suffix' => $oldShipment->getCurrency()->priceSuffix->get()
							),
							'newShipment' => array(
								'ID' => $newShipment->getID(),
								'amount' =>  $newShipment->amount->get(),
								'shippingAmount' =>  $newShipment->shippingAmount->get(),
								'taxAmount' => $newShipment->taxAmount->get(),
								'total' => ((float)$newShipment->shippingAmount->get() + (float)$newShipment->amount->get() + (float)$newShipment->taxAmount->get()),
								'prefix' => $newShipment->getCurrency()->pricePrefix->get(),
								'suffix' => $newShipment->getCurrency()->priceSuffix->get(),
								'Order' => $newShipment->order->get()->toFlatArray()
							)
						),
						'success',
						$this->translate('_ordered_item_was_successfully_moved')
					);
				}
				else
				{
					return new JSONResponse(
						array(
							'oldShipment' => array('ID' => $fromID),
							'newShipment' => array('ID' => $toID)
						),
						'failure',
						$this->translate('_this_shipping_service_has_no_available_rates')
					);
				}
			}
		}
		else
		{
			return new JSONResponse(array('status' => 'failure'));
		}
	}

	public function changeCount()
	{
		if(($id = (int)$this->request->get("id", false)) )
		{
			$count = (int)$this->request->get("count");
			$price = (float)$this->request->get("price");
			$item = OrderedItem::getInstanceByID('OrderedItem', $id, true, array('Shipment', 'Order' => 'CustomerOrder', 'ShippingService', 'Currency', 'ShippingAddress' => 'UserAddress', 'Product', 'Category'));
			$item->customerOrder->get()->loadAll();
			$history = new OrderHistory($item->customerOrder->get(), $this->user);

			$item->count->set($count);

			if ($item->price->get() != $price)
			{
				$item->price->set($item->getCurrency()->round($item->reduceBaseTaxes($price)));
			}

			$shipment = $item->shipment->get();
			$shipment->loadItems();

			if($shipment->getShippingService())
			{
				$shipmentRates = $shipment->getDeliveryZone()->getShippingRates($shipment);
				$shipment->setAvailableRates($shipmentRates);
				$shipment->setRateId($shipment->getShippingService()->getID());
			}

			$shipment->recalculateAmounts();

			$item->customerOrder->get()->save(true);
			$item->save();
			$shipment->save();

			$history->saveLog();

			return new JSONResponse(array(
				'ID' => $item->getID(),
				'Shipment' => array(
					'ID' => $shipment->getID(),
					'Order' => $item->customerOrder->get()->toFlatArray(),
					'isDeleted' => $item->isDeleted(),
					'amount' => $shipment->amount->get(),
					'downloadable' => !$shipment->isShippable(),
					'shippingAmount' => $shipment->shippingAmount->get(),
					'total' =>((float)$shipment->shippingAmount->get() + (float)$shipment->amount->get() + (float)$shipment->taxAmount->get()),
					'taxAmount' => $shipment->taxAmount->get(),
					'prefix' => $shipment->getCurrency()->pricePrefix->get(),
					'suffix' => $shipment->getCurrency()->priceSuffix->get()
				 )),
				 'success'
			 );
		}
		else
		{
			return new JSONResponse(false, 'failure', $this->translate('_error_updating_item_quantity'));
		}
	}

	public function optionForm()
	{
		$item = ActiveRecordModel::getInstanceById('OrderedItem', $this->request->get('id'), true, true);
		$item->customerOrder->get()->loadAll();

		$c = new OrderController($this->application);

		$response = $c->optionForm($item->customerOrder->get(), '');
		$response->set('currency', $item->customerOrder->get()->currency->get()->getID());
		return $response;
	}

	public function variationForm()
	{
		$this->loadLanguageFile('Frontend');
		$this->loadLanguageFile('Product');
		$this->loadLanguageFile('backend/Shipment');
		$item = ActiveRecordModel::getInstanceById('OrderedItem', $this->request->get('id'), true, true);
		$item->customerOrder->get()->loadAll();

		$c = new OrderController($this->application);

		$response = $c->variationForm($item->customerOrder->get(), '');
		$response->set('currency', $item->customerOrder->get()->currency->get()->getID());
		$response->set('variations', $item->getProduct()->getVariationData($this->application));
		return $response;
	}

	public function saveOptions()
	{
		$item = ActiveRecordModel::getInstanceByID('OrderedItem', $this->request->get('id'), OrderedItem::LOAD_DATA, OrderedItem::LOAD_REFERENCES);
		$item->customerOrder->get()->loadAll();
		foreach ($item->getProduct()->getOptions(true) as $option)
		{
			OrderController::modifyItemOption($item, $option, $this->request, OrderController::getFormFieldName($item, $option));
		}

		$item->save();
		$item->price->set($item->getPrice(true));
		$item->shipment->get()->save();
		$item->customerOrder->get()->getTotal(true);
		$item->customerOrder->get()->save();

		return $this->getItemResponse($item);
	}

	public function saveVariations()
	{
		$item = ActiveRecordModel::getInstanceByID('OrderedItem', $this->request->get('id'), OrderedItem::LOAD_DATA, OrderedItem::LOAD_REFERENCES);
		$item->customerOrder->get()->loadAll();

		$variations = $item->getProduct()->getVariationData($this->application);

		$c = new OrderController($this->application);
		if (!$c->buildVariationsValidator($item, $variations)->isValid())
		{
			return new RawResponse();
		}

		$product = $c->getVariationFromRequest($variations);
		$item->product->set($product);
		$item->setCustomPrice($product->getItemPrice($item));

		$item->save();
		$item->shipment->get()->save();
		$item->customerOrder->get()->save();

		return $this->getItemResponse($item);
	}

	public function items()
	{
		$request = $this->getRequest();
		$ids = explode(',', $request->get('item_ids'));
		$items = array();
		$this->application->getLocale()->translationManager()->loadFile('backend/Shipment');
		$set = new ProductSet();
		foreach($ids as $id)
		{
			$item = ActiveRecordModel::getInstanceByID('OrderedItem', $id, OrderedItem::LOAD_DATA);
			$item->customerOrder->get()->load();
			$item->customerOrder->get()->loadItems();
			if ($image = $item->getProduct()->defaultImage->get())
			{
				$image->load();
			}
			$items[] = $item->toArray();
			$set->add($item->getProduct()->getParent());
		}
		$response = new ActionResponse('items', $items);

		// load product options and variations
		//  pp(array_keys(ProductOption::loadOptionsForProductSet($set)));
		$response->set('allOptions', ProductOption::loadOptionsForProductSet($set));
		$response->set('variations', $set->getVariationData($this->application));

		return $response;
	}

	public function item()
	{
		$item = ActiveRecordModel::getInstanceByID('OrderedItem', $this->request->get('id'), OrderedItem::LOAD_DATA);
		return $this->getItemResponse($item);
	}

	public function downloadOptionFile()
	{
		ClassLoader::import('application.model.product.ProductOptionChoice');
		$f = select(eq('OrderedItem.ID', $this->request->get('id')),
					eq('ProductOptionChoice.optionID', $this->request->get('option')));

		$set = ActiveRecordModel::getRecordSet('OrderedItemOption', $f, array('CustomerOrder', 'OrderedItem', 'ProductOptionChoice'));
		if ($set->size())
		{
			return new ObjectFileResponse($set->get(0)->getFile());
		}
	}

	private function getItemResponse(OrderedItem $item)
	{
		$item->customerOrder->get()->load();
		$item->customerOrder->get()->loadItems();

		if ($image = $item->getProduct()->defaultImage->get())
		{
			$image->load();
		}

		$this->application->getLocale()->translationManager()->loadFile('backend/Shipment');

		$response = new ActionResponse('item', $item->toArray());

		// load product options and variations
		$response->set('allOptions', ProductOption::loadOptionsForProductSet($item->getProduct()->getParent()->initSet()));
		$response->set('variations', $item->getProduct()->getParent()->initSet()->getVariationData($this->application));

		return $response;
	}
}

?>
