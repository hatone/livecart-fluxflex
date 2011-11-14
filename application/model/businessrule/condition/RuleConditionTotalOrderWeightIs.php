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
ClassLoader::import('application.model.businessrule.RuleCondition');
ClassLoader::import('application.model.businessrule.interface.RuleOrderCondition');

/**
 *
 * @author Integry Systems
 * @package application.model.weight-pricing
 */
class RuleConditionTotalOrderWeightIs extends RuleCondition implements RuleOrderCondition
{
	public function isApplicable()
	{
		$order = $this->getContext()->getOrder();
		if(false == $order instanceof CustomerOrder)
		{
			return false;
		}
		$items = $order->getOrderedItems();
		$totalWeight = 0;
		foreach($items as $item)
		{
			$product = $item->productID->get();
			if($product)
			{
				$totalWeight += (float)$product->shippingWeight->get() * $item->count->get();
			}
		}
		return $this->compareValues($this->toGrams($totalWeight), $this->params['subTotal']);
	}

	private function toGrams($weight)
	{
		return $weight * 1000;
	}
}

?>
