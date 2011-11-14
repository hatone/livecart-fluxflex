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

ClassLoader::import('application.model.user.SessionUser');
ClassLoader::import('application.model.order.CustomerOrder');
ClassLoader::import('application.model.user.UserAddress');
ClassLoader::import('application.model.businessrule.RuleProductContainer');

/**
 *
 * @package application.model.order
 * @author Integry Systems <http://integry.com>
 */
class SessionOrder
{
	private static $instance = null;

	/**
	 * Get CustomerOrder instance from session
	 *
	 * @return CustomerOrder
	 */
	public static function getOrder()
	{
		if (self::$instance)
		{
			return self::$instance;
		}

		$session = new Session();

		$id = $session->get('CustomerOrder');
		if ($id)
		{
			try
			{
				$instance = CustomerOrder::getInstanceById($id, true);

				if (!$instance->getOrderedItems())
				{
					$instance->loadItems();
				}

				$instance->isSyncedToSession = true;
			}
			catch (ARNotFoundException $e)
			{
				unset($instance);
			}
		}

		if (!isset($instance))
		{
			$userId = SessionUser::getUser()->getID();

			// get the last unfinalized order by this user
			if ($userId > 0)
			{
				$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle('CustomerOrder', 'userID'), $userId));
				$f->mergeCondition(new NotEqualsCond(new ARFieldHandle('CustomerOrder', 'isFinalized'), true));
				$f->setOrder(new ARFieldHandle('CustomerOrder', 'ID'), 'DESC');
				$f->setLimit(1);
				$orders = ActiveRecordModel::getRecordSet('CustomerOrder', $f);
				if ($orders->size())
				{
					$instance = $orders->get(0);
				}
			}
		}

		if (!isset($instance))
		{
			$instance = CustomerOrder::getNewInstance(User::getNewInstance(0));
			$instance->user->set(NULL);
		}

		if (!$instance->user->get() && SessionUser::getUser()->getID() > 0)
		{
			$instance->setUser(SessionUser::getUser());
			$instance->save();
		}

		if ($instance->isFinalized->get())
		{
			$session->unsetValue('CustomerOrder');
			return self::getOrder();
		}

		self::setOrder($instance);

		return $instance;
	}

	public static function setOrder(CustomerOrder $order)
	{
		$session = new Session();
		$session->set('CustomerOrder', $order->getID());

		$currency = $order->getCurrency();
		$currID = $currency->getID();

		$total = $order->getTotal();
		$orderArray = array('total' => array($currID => $total));
		$orderArray['formattedTotal'] = array($currID => $currency->getFormattedPrice($orderArray['total'][$currID]));
		$orderArray['basketCount'] = $order->getShoppingCartItemCount();
		$orderArray['currencyID'] = $currID;

		$isOrderable = $order->isOrderable();
		$orderArray['isOrderable'] = is_bool($isOrderable) ? $isOrderable : false;

		$items = array();
		foreach ($order->getPurchasedItems() as $item)
		{
			$items[] = $item->toArray();
		}

		$orderArray['items'] = new RuleOrderContainer($items);
		$orderArray['items']->setCoupons($order->getCoupons());
		$orderArray['items']->setTotal($total);

		$session->set('orderData', $orderArray);

		self::$instance = $order;
	}

	public static function getOrderItems()
	{
		$session = new Session();
		$data = $session->get('orderData');
		if (isset($data['items']))
		{
			return $data['items'];
		}
	}

	public static function getOrderData()
	{
		self::setOrder(self::getOrder());
		$session = new Session();
		return $session->get('orderData');
	}

	public static function getEstimateAddress()
	{
		$session = new Session();

		if ($address = $session->get('shippingEstimateAddress'))
		{
			return unserialize($address);
		}
		else
		{
			$order = self::getOrder();
			if ($order->shippingAddress->get())
			{
				return $order->shippingAddress->get();
			}

			$user = $order->user->get();
			if ($user && !$user->isAnonymous())
			{
				$user->load(true);
				foreach (array('defaultShippingAddress', 'defaultBillingAddress') as $key)
				{
					if ($address = $user->$key->get())
					{
						$address->load(array('UserAddress'));
						$address->userAddress->get()->load();
						return $address->userAddress->get();
					}
				}
			}
		}

		return self::getDefaultEstimateAddress();
	}

	public static function getDefaultEstimateAddress()
	{
		$config = ActiveRecordModel::getApplication()->getConfig();
		$address = UserAddress::getNewInstance();
		$address->countryID->set($config->get('DEF_COUNTRY'));

		if ($state = $config->get('DEF_STATE'))
		{
			$address->state->set(State::getInstanceByID($config->get('DEF_STATE')));
		}

		return $address;
	}

	public static function setEstimateAddress(UserAddress $address)
	{
		$order = self::getOrder();
		$estimateAddress = clone $address;
		$estimateAddress->removeSpecification();
		$session = new Session();
		$session->set('shippingEstimateAddress', $estimateAddress);
	}

	public static function save(CustomerOrder $order)
	{
		// mark shipment data as modified - to force saving
		$order->getShipments();

		$order->save();
		self::setOrder($order);
	}

	public static function destroy()
	{
		$session = new Session();
		$session->unsetValue('CustomerOrder');
		$session->unsetValue('orderData');
	}
}

?>
