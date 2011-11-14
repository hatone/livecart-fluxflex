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
 * Intermediate entity for assigning Roles to UserGroups.
 *
 * @package application.model.role
 * @author Integry Systems <http://integry.com>
 */
class AccessControlAssociation extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName("AccessControlAssociation");

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("roleID", "Role", "ID", "Role", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("userGroupID", "UserGroup", "ID", "UserGroup", ARInteger::instance()));
	}

	/**
	 * Gets an existing record instance (persisted on a database).
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * @return AccessControlAssociation
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords, $data);
	}

	/**
	 * Associate new role to user group
	 *
	 * @param UserGroup $userGroup User group
	 * @param Role $role Associate group with this role
	 * @return Tax
	 */
	public static function getNewInstance(UserGroup $userGroup, Role $role)
	{
	  	$instance = ActiveRecord::getNewInstance(__CLASS__);
	  	$instance->userGroup->set($userGroup);
		$instance->role->set($role);

	  	return $instance;
	}

	/**
	 * Load groups to roles associations from database
	 *
	 * @param Role $role Associate group with this role
	 * @param ARSelectFilter $filter
	 * @param bool $loadReferencedRecords
	 *
	 * @return ARSet
	 */
	public static function getRecordSet(ARSelectFilter $filter, $loadReferencedRecords = false)
	{
		return parent::getRecordSet(__CLASS__, $filter, $loadReferencedRecords);
	}

	/**
	 * Load groups to roles associations from database using specified role
	 *
	 * @param Role $role Role
	 * @param ARSelectFilter $filter
	 * @param bool $loadReferencedRecords
	 *
	 * @return ARSet
	 */
	public static function getRecordSetByRole(Role $role, ARSelectFilter $filter, $loadReferencedRecords = false)
	{
		$filter->mergeCondition(new EqualsCond(new ARFieldHandle(__CLASS__, "roleID"), $role->getID()));

		return self::getRecordSet($filter, $loadReferencedRecords = false);
	}

	/**
	 * Load groups to roles associations from database using specified group
	 *
	 * @param UserGroup $userGroup User group
	 * @param ARSelectFilter $filter
	 * @param bool $loadReferencedRecords
	 *
	 * @return ARSet
	 */
	public static function getRecordSetByUserGroup(UserGroup $userGroup, ARSelectFilter $filter, $loadReferencedRecords = false)
	{
		$filter->mergeCondition(new EqualsCond(new ARFieldHandle(__CLASS__, "userGroupID"), $userGroup->getID()));

		return self::getRecordSet($filter, $loadReferencedRecords);
	}


	/**
	 * Load groups to roles associations as array from database using specified group
	 *
	 * @param UserGroup $userGroup User group
	 * @param ARSelectFilter $filter
	 * @param bool $loadReferencedRecords
	 *
	 * @return Array
	 */
	public static function getRecordSetArrayByUserGroup(UserGroup $userGroup, ARSelectFilter $filter, $loadReferencedRecords = false)
	{
		$filter->mergeCondition(new EqualsCond(new ARFieldHandle(__CLASS__, "userGroupID"), $userGroup->getID()));

		return self::getRecordSetArray(__CLASS__, $filter, $loadReferencedRecords);
	}

	public function save()
	{
		if ($this->isModified())
		{
			@unlink($this->userGroup->get()->getRoleCacheFile());
		}

		return parent::save();
	}
}

?>
