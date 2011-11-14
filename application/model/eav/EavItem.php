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
ClassLoader::import('application.model.eav.EavField');
ClassLoader::import('application.model.eav.EavValue');

/**
 * Links a pre-defined attribute value to a product
 *
 * @package application.model.specification
 * @author Integry Systems <http://integry.com>
 */
class EavItem extends EavItemCommon
{
	public static function getFieldClass()
	{
		return 'EavField';
	}

	public static function getOwnerClass()
	{
		return 'EavObject';
	}

	public static function getValueClass()
	{
		return 'EavValue';
	}

	public static function getFieldIDColumnName()
	{
		return 'fieldID';
	}

	public static function getOwnerIDColumnName()
	{
		return 'objectID';
	}

	public static function getValueIDColumnName()
	{
		return 'valueID';
	}

	public function getOwnerVarName()
	{
		return 'object';
	}

	public function getField()
	{
		return $this->field;
	}

	public function getValue()
	{
		return $this->value;
	}

	public static function defineSchema($className = __CLASS__)
	{
		return parent::defineSchema($className);
	}

	public static function getNewInstance(EavObject $product, EavField $field, EavValue $value)
	{
		return parent::getNewInstance(__CLASS__, $product, $field, $value);
	}

	public static function restoreInstance(EavObject $product, EavField $field, EavValue $value)
	{
		return parent::restoreInstance(__CLASS__, $product, $field, $value);
	}

	public function set(EavValue $value)
	{
	  	return parent::set($value);
	}

	public function toArray()
	{
		$arr = parent::toArray();
		$arr['EavField'] = $arr['Field'];
		return $arr;
	}
}

?>
