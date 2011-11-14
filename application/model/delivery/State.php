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
 * Pre-defined address state/province. When there are states defined for a country the system
 * will always offer to select the address from a drop-down list. Otherwise the user has to
 * enter the address manually. It would be a good idea to pre-define states for the countries
 * your store serves (the ones that bring the most orders).
 *
 * @package application.model.delivery
 * @author Integry Systems <http://integry.com>
 */
class State extends ActiveRecordModel
{
	/**
	 * Define database schema
	 */
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARField("countryID", ARChar::instance(2)));
		$schema->registerField(new ARField("code", ARVarchar::instance(40)));
		$schema->registerField(new ARField("name", ARVarchar::instance(100)));
		$schema->registerField(new ARField("subdivisionType", ARVarchar::instance(60)));
	}

	/*####################  Static method implementations ####################*/

	/**
	 * Gets an existing record instance (persisted on a database).
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * @return ActiveRecord
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords, $data);
	}

	/*####################  Instance retrieval ####################*/

	public static function getStatesByCountry($countryCode)
	{
		$f = new ARSelectFilter();
		$f->setCondition(new EqualsCond(new ARFieldHandle('State', 'countryID'), $countryCode));
		$f->setOrder(new ARFieldHandle('State', 'name'));
		$stateArray = ActiveRecordModel::getRecordSetArray('State', $f);

		$states = array();
		foreach ($stateArray as $state)
		{
			$states[$state['ID']] = $state['name'];
		}

		return $states;
	}

	public static function getAllStates()
	{
		return ActiveRecordModel::getRecordSet(__CLASS__, new ARSelectFilter());
	}

	public static function getAllStatesArray()
	{
		return ActiveRecordModel::getRecordSetArray(__CLASS__, new ARSelectFilter());
	}

	public static function getStateIDByName($countryCode, $name)
	{
		$f = new ARSelectFilter();
		$f->setCondition(new EqualsCond(new ARFieldHandle('State', 'countryID'), $countryCode));

		$nameCond = new EqualsCond(new ARFieldHandle('State', 'name'), $name);
		$nameCond->addOr(new EqualsCond(new ARFieldHandle('State', 'code'), $name));
		$f->mergeCondition($nameCond);

		$f->setOrder(new ARFieldHandle('State', 'name'));
		$f->setLimit(1);
		$stateArray = ActiveRecordModel::getRecordSetArray('State', $f);

		if ($stateArray)
		{
			return $stateArray[0]['ID'];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Provides an additional verification that state belongs to the particular country
	 *
	 * @return ActiveRecord
	 */
	public static function getStateByIDAndCountry($stateID, $countryID)
	{
		$f = new ARSelectFilter();
		$f->setCondition(new EqualsCond(new ARFieldHandle('State', 'ID'), $stateID));
		$f->mergeCondition(new EqualsCond(new ARFieldHandle('State', 'countryID'), $countryID));

		$f->setLimit(1);
		$states = ActiveRecordModel::getRecordSet('State', $f);

		if ($states)
		{
			return $states->get(0);
		}
		else
		{
			return null;
		}
	}
}

?>
