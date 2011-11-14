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

ClassLoader::import('application.model.eavcommon.EavMultiValueItemCommon');

/**
 * Links multiple pre-defined attribute values (of the same attribute) to a product
 *
 * @package application.model.specification
 * @author Integry Systems <http://integry.com>
 */
class MultiValueSpecificationItem extends EavMultiValueItemCommon
{
	public function getItemClassName()
	{
		return 'SpecificationItem';
	}

	public static function getNewInstance(Product $product, SpecField $field, $value = false)
	{
		return parent::getNewInstance(__CLASS__, $product, $field, $value);
	}

	public static function restoreInstance(Product $product, SpecField $field, $specValues)
	{
		return parent::restoreInstance(__CLASS__, $product, $field, $specValues);
	}

	protected function __construct(Product $product, SpecField $field)
	{
		parent::__construct($product, $field);
	}

	public function set(SpecFieldValue $value)
	{
	  	return parent::set($value);
	}

	public function removeValue(SpecFieldValue $value)
	{
	  	return parent::removeValue($value);
	}

	protected function setItem(SpecificationItem $item)
	{
		return parent::setItem($item);
	}
}

?>
