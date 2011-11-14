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

ClassLoader::import("application.model.order.CustomerOrder");
ClassLoader::import("application.model.user.UserAddress");

/**
 * Express checkout data container
 *
 * @package application.model.user
 * @author Integry Systems <http://integry.com> 
 */
class ExpressCheckout extends ActiveRecordModel
{
	/**
	 * Define database schema
	 */
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);
		
		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("addressID", "address", "ID", 'UserAddress', ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("orderID", "order", "ID", 'CustomerOrder', ARInteger::instance()));

		$schema->registerField(new ARField("method", ARVarchar::instance(40)));
		$schema->registerField(new ARField("paymentData", ARText::instance()));
	}	
	
	/*####################  Static method implementations ####################*/	
	
	public static function getNewInstance(CustomerOrder $order, ExpressPayment $handler)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->order->set($order);
		$instance->method->set(get_class($handler));
		return $instance;
	}
	
	public static function getInstanceByOrder(CustomerOrder $order)
	{
		$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle(__CLASS__, 'orderID'), $order->getID()));
		$s = self::getRecordSet(__CLASS__, $f);
		if ($s->size())
		{
			return $s->get(0);
		}
	}
	
	/*####################  Saving ####################*/	
	
	public function deleteInstancesByOrder(CustomerOrder $order)
	{
		// remove other ExpressCheckout instances for this order
		$f = new ARDeleteFilter();
		$f->setCondition(new EqualsCond(new ARFieldHandle('ExpressCheckout', 'orderID'), $order->getID()));
		ActiveRecordModel::deleteRecordSet('ExpressCheckout', $f);
	}
	
	protected function insert()
	{
		$this->deleteInstancesByOrder($this->order->get());
		
		return parent::insert();
	}
	
	/*####################  Get related objects ####################*/	
	
	public function getHandler(TransactionDetails $transaction = null)
	{
		$handler = $this->getApplication()->getExpressPaymentHandler($this->method->get(), $transaction);
		$handler->setData(unserialize($this->paymentData->get()));
		return $handler;		
	}
	
	public function getTransactionDetails()
	{
		return $this->getHandler()->getDetails();
	}	
}

?>
