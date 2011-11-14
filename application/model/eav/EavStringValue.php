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

ClassLoader::import('application.model.eav.EavValueSpecification');

/**
 * String attribute value assigned to a particular product.
 *
 * @package application.model.specification
 * @author Integry Systems <http://integry.com>
 */
class EavStringValue extends EavValueSpecification
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = parent::defineSchema($className);
		$schema->registerField(new ARField("value", ARArray::instance()));
	}

	public static function getNewInstance(EavObject $object, EavField $field, $value)
	{
	  	return parent::getNewInstance(__CLASS__, $object, $field, $value);
	}

	public static function restoreInstance(EavObject $object, EavField $field, $value)
	{
		$specItem = parent::restoreInstance(__CLASS__, $object, $field, $value);
		$specItem->value->set(unserialize($value));

		$specItem->resetModifiedStatus();

		return $specItem;
	}
}

?>
