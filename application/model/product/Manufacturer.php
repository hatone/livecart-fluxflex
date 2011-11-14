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

ClassLoader::import('application.model.ActiveRecordModel');
ClassLoader::import('application.model.eav.EavAble');
ClassLoader::import('application.model.eav.EavObject');
ClassLoader::import('application.model.product.ManufacturerImage');

/**
 * Defines a product manufacturer. Each product can be assigned to one manufacturer.
 * Keeping manufacturers as a separate entity allows to manipulate them more easily and
 * provide more effective product filtering (search by manufacturers).
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class Manufacturer extends ActiveRecordModel implements EavAble
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARField("name", ARVarchar::instance(60)));
		$schema->registerField(new ARForeignKeyField("defaultImageID", "ManufacturerImage", "ID", null, ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("eavObjectID", "eavObject", "ID", 'EavObject', ARInteger::instance()), false);
		$schema->registerAutoReference('defaultImageID');
	}

	public static function getNewInstance($name)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->name->set($name);
		return $instance;
	}

	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData = false, $loadReferencedRecords = false);
	}

	public static function getInstanceByName($name)
	{
		$filter = new ARSelectFilter();
		$filter->setCondition(new EqualsCond(new ARFieldHandle('Manufacturer', 'name'), $name));
		$filter->setLimit(1);
		$set = ActiveRecordModel::getRecordSet('Manufacturer', $filter);
		if ($set->size() > 0)
		{
			return $set->get(0);
		}
		else
		{
			return self::getNewInstance($name);
		}
	}
}

?>
