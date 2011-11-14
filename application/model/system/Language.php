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
 * System language logic - adding, removing or enabling languages.
 *
 * @author Integry Systems <http://integry.com>
 * @package application.model.system
 */
class Language extends ActiveRecordModel
{
   	/**
	 * Languages schema definition
	 * @param string $className
	 * @todo code must be Unique
	 */
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName("Language");

		$schema->registerField(new ARPrimaryKeyField("ID", ARChar::instance(2)));
		$schema->registerField(new ARField("isEnabled", ARBool::instance()));
		$schema->registerField(new ARField("isDefault", ARBool::instance()));
		$schema->registerField(new ARField("position", ARInteger::instance()));
	}

	/**
	 * Gets language by its id.
	 * @param string(2) $ID
	 * @return Language
	 */
	public static function getInstanceByID($ID)
	{
		return ActiveRecord::getInstanceByID("Language", $ID, true);
	}

	/**
	 * Checks whether the language is systems default language
	 * @return bool
	 */
	public function isDefault()
	{
	  	return (bool)$this->isDefault->get();
	}

	/**
	 * Changes default language status
	 * @param bool $isDefault (sets as default if true, unsets default status if false)
	 */
	public function setAsDefault($isDefault = 1)
	{
	  	$this->isDefault->set($isDefault == 1 ? 1 : 0);
	  	return true;
	}

	/**
	 * Changes language status to enabled or disabled
	 * @param bool $isEnabled (sets as enabled if true, unsets enabled status if false)
	 */
	public function setAsEnabled($isEnabled = 1)
	{
	  	$this->isEnabled->set($isEnabled == 1 ? 1 : 0);
	  	return true;
	}

	public function save($forceOperation = 0)
	{
		self::deleteCache();

		return parent::save($forceOperation);
	}

	public static function deleteById($id)
	{
		self::deleteCache();

		// make sure the language record exists
		$inst = ActiveRecord::getInstanceById('Language', $id, true);

		// make sure it's not the default currency
		if (true != $inst->isDefault->get())
		{
			ActiveRecord::deleteByID('Language', $id);
			return true;
		}
		else
		{
		  	return false;
		}
	}

	public static function deleteCache()
	{
		$cacheFile = self::getCacheFile();
		if (file_exists($cacheFile))
		{
			unlink($cacheFile);
		}
	}

	public static function getCacheFile()
	{
		return ClassLoader::getRealPath('cache') . '/languages.php';
	}

	protected function insert()
	{
		$this->setLastPosition();

		parent::insert();
	}

	public function toArray()
	{
	  	$array = parent::toArray();

	  	$info = self::getApplication()->getLocale()->info();
		$array['name'] = $info->getLanguageName($array['ID']);
	  	$array['originalName'] = $info->getOriginalLanguageName($array['ID']);

		if (file_exists(ClassLoader::getRealPath('public.image.localeflag') . '/' . $array['ID'] . '.png'))
		{
		  	$array['image'] = 'image/localeflag/' . $array['ID'] . '.png';
		}

		return $array;
	}

	/**
	 *
	 * PHP segfaults (5.2.3 / mod_php) on installation time when an unsaved instance is destructed,
	 * so to avoid this, we're simply not destructing the Language instances (and there's no need for that anyway)
	 */
	public function __destruct()
	{

	}
}

?>
