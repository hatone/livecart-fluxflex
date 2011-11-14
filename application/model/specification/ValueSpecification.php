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

ClassLoader::import('application.model.eavcommon.EavValueSpecificationCommon');

/**
 * An attribute value that is assigned to a particular product.
 * Concrete attribute value types (string, number, date, etc.) are defined by subclasses.
 *
 * @package application.model.specification
 * @author Integry Systems <http://integry.com>
 */
abstract class ValueSpecification extends EavValueSpecificationCommon
{
	public static function getFieldClass()
	{
		return 'SpecField';
	}

	public static function getOwnerClass()
	{
		return 'Product';
	}

	public static function getFieldIDColumnName()
	{
		return 'specFieldID';
	}

	public static function getOwnerIDColumnName()
	{
		return 'productID';
	}

	public static function getNewInstance($class, Product $product, SpecField $field, $value)
	{
		return parent::getNewInstance($class, $product, $field, $value);
	}

	public static function restoreInstance($class, Product $product, SpecField $field, $value)
	{
		return parent::restoreInstance($class, $product, $field, $value);
	}
}

?>
