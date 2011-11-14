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

ClassLoader::import("framework.request.validator.check.Check");

/**
 * Checks if product SKU is unique
 *
 * @package application.helper.check
 * @author Integry Systems 
 */
class IsUniqueSkuCheck extends Check
{
	var $product;
	
	public function __construct($errorMessage, Product $product)
	{
		parent::__construct($errorMessage);
		$this->product = $product;  
	}
	
	public function isValid($value)
	{
		$filter = new ARSelectFilter();
		$cond = new EqualsCond(new ARFieldHandle('Product', 'sku'), $value);
		if ($this->product->getID())
		{
		  	$cond->addAND(new NotEqualsCond(new ARFieldHandle('Product', 'ID'), $this->product->getID()));
		}
		$filter->setCondition($cond);	
		
		return (ActiveRecordModel::getRecordCount('Product', $filter) == 0);
	}
}

?>
