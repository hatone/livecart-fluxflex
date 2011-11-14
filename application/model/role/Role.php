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
 * Roles allow to fine-grain user (UserGroup) access to controller classes and methods.
 * For the time being the roles are only used for the backend area.
 *
 * @package application.model.roles
 * @author Integry Systems <http://integry.com>
 */
class Role extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName("Role");

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance(10)));
		$schema->registerField(new ARField("name", ARText::instance(150)));
	}

	/**
	 * Gets an existing record instance (persisted on a database).
	 * @param integer $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * @return Role
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords, $data);
	}

	/**
	 * Gets an existing record instance by specifying role name
	 * @param string $name
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * @return Role
	 */
	public static function getInstanceByName($name)
	{
		$filter = new ARSelectFilter();
		$filter->setCondition(new EqualsCond(new ARFieldHandle(__CLASS__, "name"), $name));

		$arSet = self::getRecordSet($filter);
		return $arSet->getTotalRecordCount() > 0 ? $arSet->get(0) : null;
	}

	/**
	 * Create new role rate
	 *
	 * @param string $name New role name
	 * @return Role
	 */
	public static function getNewInstance($name)
	{
	  	$instance = ActiveRecord::getNewInstance(__CLASS__);
	  	$instance->name->set($name);

	  	return $instance;
	}

	/**
	 * Load roles record set
	 *
	 * @param ARSelectFilter $filter
	 * @param bool $loadReferencedRecords
	 *
	 * @return ARSet
	 */
	public static function getRecordSet(ARSelectFilter $filter, $loadReferencedRecords = false)
	{
		$filter->setOrder(new ARFieldHandle(__CLASS__, "name"), 'ASC');

		return parent::getRecordSet(__CLASS__, $filter, $loadReferencedRecords);
	}

	public static function addNewRolesNames($roleNames, $deleteOther = false)
	{
		// unset meta- roles
		if ($i = array_search('login', $roleNames))
		{
			unset($roleNames[$i]);
		}

		if(!is_array($roleNames) || empty($roleNames)) return;

		$filter = new ARSelectFilter();
		$deleteFilter = new ARDeleteFilter();

		$condition = new EqualsCond(new ARFieldHandle(__CLASS__, "name"), $roleNames[0]);
		$deleteCondition = new NotEqualsCond(new ARFieldHandle(__CLASS__, "name"), $roleNames[0]);
		foreach($roleNames as $roleName)
		{
			$condition->addOR(new EqualsCond(new ARFieldHandle(__CLASS__, "name"), $roleName));
			$deleteCondition->addAnd(new NotEqualsCond(new ARFieldHandle(__CLASS__, "name"), $roleName));
		}

		$filter->setCondition($condition);
		$deleteFilter->setCondition($deleteCondition);

		if($deleteOther)
		{
			self::deleteRecordSet(__CLASS__, $deleteFilter);
		}

   		// Find new roles
		$invertedRoleNames = array_flip($roleNames);
		foreach(self::getRecordSet($filter) as $role)
		{
			if(isset($invertedRoleNames[$role->name->get()]))
			{
				unset($invertedRoleNames[$role->name->get()]);
			}
		}
		// Add new roles to database
		foreach($invertedRoleNames as $role => $value)
		{
			if(!empty($role))
			{
				$newRole = Role::getNewInstance($role);
				$newRole->save();
			}
		}
	}

	public static function cleanUp()
	{
		ClassLoader::import('framework.roles.RolesDirectoryParser');
		$rolesCacheDir = ClassLoader::getRealPath('cache.roles');
		if(!is_dir($rolesCacheDir))
		{
			mkdir($rolesCacheDir, 0777, true);
		}

		$rolesDirectoryParser = new RolesDirectoryParser(ClassLoader::getRealPath('application.controller.backend'), $rolesCacheDir);
		$roleNames = array();
		foreach($rolesDirectoryParser->getClassParsers() as $classParser)
		{
			$parserRoleNames = array_flip($classParser->getRolesNames());
			$roleNames = array_merge($roleNames, $parserRoleNames);
		}

		// @todo: change to true when the issue with deleting existing roles is clearly identified and fixed
		self::addNewRolesNames(array_keys($roleNames), false);
	}
}

?>
