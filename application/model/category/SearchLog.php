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

/**
 *
 * @package application.model.category
 * @author Integry Systems <http://integry.com>
 */
class SearchLog extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField('ID', ARInteger::instance()));
		$schema->registerField(new ARField('keywords', ARVarchar::instance(100)));
		$schema->registerField(new ARField('ip', ARInteger::instance()));
		$schema->registerField(new ARField('time', ARDateTime::instance()));
	}

	/*####################  Static method implementations ####################*/

	public static function getNewInstance($query)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->keywords->set($query);
		return $instance;
	}

	/*####################  Saving ####################*/

	protected function insert()
	{
		$this->time->set(new ARSerializableDateTime());
		return parent::insert();
	}
}

?>
