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

ClassLoader::import('application.model.eavcommon.EavItemCommon');
ClassLoader::import('application.model.product.Product');
ClassLoader::import('application.model.category.SpecField');
ClassLoader::import('application.model.category.SpecFieldValue');

/**
 * Links a pre-defined attribute value to a product
 *
 * @package application.model.specification
 * @author Integry Systems <http://integry.com>
 */
class SpecificationItem extends EavItemCommon
{
	public static function getFieldClass()
	{
		return 'SpecField';
	}

	public static function getOwnerClass()
	{
		return 'Product';
	}

	public static function getValueClass()
	{
		return 'SpecFieldValue';
	}

	public static function getFieldIDColumnName()
	{
		return 'specFieldID';
	}

	public static function getOwnerIDColumnName()
	{
		return 'productID';
	}

	public static function getValueIDColumnName()
	{
		return 'specFieldValueID';
	}

	public function getOwnerVarName()
	{
		return 'product';
	}

	public function getField()
	{
		return $this->specField;
	}

	public function getValue()
	{
		return $this->specFieldValue;
	}

	public static function defineSchema($className = __CLASS__)
	{
		return parent::defineSchema($className);
	}

	public static function getNewInstance(Product $product, SpecField $field, SpecFieldValue $value)
	{
		return parent::getNewInstance(__CLASS__, $product, $field, $value);
	}

	public static function restoreInstance(Product $product, SpecField $field, SpecFieldValue $value)
	{
		return parent::restoreInstance(__CLASS__, $product, $field, $value);
	}

	public function set(SpecFieldValue $value)
	{
	  	return parent::set($value);
	}

}

?>
