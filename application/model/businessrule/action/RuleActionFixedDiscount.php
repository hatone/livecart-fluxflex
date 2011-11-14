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

ClassLoader::import('application.model.businessrule.action.RuleActionPercentageDiscount');
ClassLoader::import('application.model.businessrule.interface.RuleItemAction');
ClassLoader::import('application.model.businessrule.interface.RuleOrderAction');

/**
 *
 * @author Integry Systems
 * @package application.model.businessrule.action
 */
class RuleActionFixedDiscount extends RuleActionPercentageDiscount implements RuleOrderAction, RuleItemAction
{
	public function applyToOrder(CustomerOrder $order)
	{
		$amount = $order->getCurrency()->convertAmount(CustomerOrder::getApplication()->getDefaultCurrency(), $this->getDiscountAmount($order->totalAmount->get()));
		$orderDiscount = OrderDiscount::getNewInstance($order);
		$orderDiscount->amount->set($amount);
		$orderDiscount->description->set($this->parentCondition->getParam('name_lang'));
		$order->registerOrderDiscount($orderDiscount);
	}

	protected function getDiscountAmount($price)
	{
		return $this->getParam('amount');
	}

	public static function getSortOrder()
	{
		return 2;
	}

	public function isItemAction()
	{
		return DiscountAction::TYPE_ORDER_DISCOUNT != $this->getParam('type');
	}

	public function isOrderAction()
	{
		return DiscountAction::TYPE_ORDER_DISCOUNT == $this->getParam('type');
	}
}

?>
