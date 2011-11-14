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

/**
 * Defines a tax class
 *
 * @package application.model.delivery
 * @author Integry Systems <http://integry.com>
 */
class TaxClass extends MultilingualObject
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARField("name", ARArray::instance()));
		$schema->registerField(new ARField("position", ARInteger::instance(2)));
	}

	/**
	 * Gets an existing record instance (persisted on a database).
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * @return TaxClass
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords, $data);
	}

	/**
	 * Create new shipping class
	 *
	 * @param string $$defaultLanguageName Type name spelled in default language
	 * @return TaxClass
	 */
	public static function getNewInstance($defaultLanguageName)
	{
	  	$instance = ActiveRecord::getNewInstance(__CLASS__);
	  	$instance->setValueByLang('name', null, $defaultLanguageName);

	  	return $instance;
	}

	/**
	 * Load record set
	 *
	 * @param ARSelectFilter $filter
	 * @param bool $loadReferencedRecords
	 *
	 * @return ARSet
	 */
	public static function getRecordSet(ARSelectFilter $filter, $loadReferencedRecords = false)
	{
		$filter->setOrder(new ARFieldHandle(__CLASS__, 'position'), ARSelectFilter::ORDER_ASC);
		return parent::getRecordSet(__CLASS__, $filter, $loadReferencedRecords);
	}

	/**
	 * Get a list of all existing classes
	 *
	 * @param boolean $loadReferencedRecords Load referenced records
	 *
	 * @return ARSet
	 */
	public static function getAllClasses($loadReferencedRecords = false)
	{
		$f = select();
		$f->setOrder(f('TaxClass.position'));

		return self::getRecordSet($f, $loadReferencedRecords);
	}

	public static function findByName($name)
	{
		$f = select(
				new EqualsCond(
					MultiLingualObject::getLangSearchHandle(
						new ARFieldHandle('TaxClass', 'name'),
						self::getApplication()->getDefaultLanguageCode()
					),
					$name
				)
			);

		return ActiveRecordModel::getRecordSet('TaxClass', $f)->get(0);
	}

	protected function insert()
	{
	  	$this->setLastPosition();

		parent::insert();
	}
}

?>
