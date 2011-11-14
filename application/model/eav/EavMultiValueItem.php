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
class EavMultiValueItem extends EavMultiValueItemCommon
{
	public function getItemClassName()
	{
		return 'EavItem';
	}

	public static function getNewInstance(EavObject $product, EavField $field, $value = false)
	{
		return parent::getNewInstance(__CLASS__, $product, $field, $value);
	}

	public static function restoreInstance(EavObject $product, EavField $field, $specValues)
	{
		return parent::restoreInstance(__CLASS__, $product, $field, $specValues);
	}

	protected function __construct(EavObject $product, EavField $field)
	{
		parent::__construct($product, $field);
	}

	public function set(EavValue $value)
	{
	  	return parent::set($value);
	}

	public function removeValue(EavValue $value)
	{
	  	return parent::removeValue($value);
	}

	protected function setItem(EavItem $item)
	{
		return parent::setItem($item);
	}
}

?>
