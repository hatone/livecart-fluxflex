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

ClassLoader::import("application.model.ActiveRecordModel");

/**
 * News post entry
 *
 * @package application.model.news
 * @author Integry Systems <http://integry.com>
 */
class NewsPost extends MultilingualObject
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName(__CLASS__);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));	
		$schema->registerField(new ARField("isEnabled", ARBool::instance()));
		$schema->registerField(new ARField("position", ARInteger::instance()));
		$schema->registerField(new ARField("time", ARDateTime::instance()));
		$schema->registerField(new ARField("title", ARArray::instance()));
		$schema->registerField(new ARField("text", ARArray::instance()));
		$schema->registerField(new ARField("moreText", ARArray::instance()));
	}
	
	protected function insert()
	{
	  	$this->setLastPosition();	  			
		return parent::insert();
	}	
}

?>
