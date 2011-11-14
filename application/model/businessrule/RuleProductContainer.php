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

ClassLoader::import('application.model.businessrule.interface.BusinessRuleProductInterface');

/**
 * Implements setItemPrice and getItemPrice methods of OrderedItem
 *
 * @author Integry Systems
 * @package application.model.businessrule
 */
class RuleProductContainer implements BusinessRuleProductInterface
{
	private $price;
	private $product;
	private $count = 1;

	public function __construct($product)
	{
		$this->product = $product;
	}

	public function getProduct()
	{
		return $this->product;
	}

	public function setItemPrice($price)
	{
		$this->price = $price;
	}

	public function getPriceWithoutTax()
	{
		return $this->price;
	}

	public function setCount($count)
	{
		$this->count = $count;
	}

	public function getCount()
	{
		return $this->count;
	}

	public function getSubTotal()
	{
		return $this->getPriceWithoutTax() * $this->getCount();
	}

	public static function createFromArray($product)
	{
		$product = array_intersect_key($product, array_flip(array('ID', 'sku', 'manufacturerID', 'categoryID', 'categoryIntervalCache')));
		return new RuleProductContainer($product);
	}

	public static function createFromOrderedItem(OrderedItem $item)
	{
		if (!$product = $item->getProduct())
		{
			return;
		}

		$instance = self::createFromArray($product->toArray());
		$instance->setCount($item->getCount());
		$instance->setItemPrice($item->getPrice());

		return $instance;
	}
}

?>
