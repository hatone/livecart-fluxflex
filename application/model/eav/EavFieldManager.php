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

ClassLoader::import('application.model.eav.EavField');
ClassLoader::import('application.model.eavcommon.EavFieldCommon');
ClassLoader::import('application.model.eavcommon.iEavFieldManager');
ClassLoader::import('application.model.eavcommon.EavSpecificationManagerCommon');

/**
 * Custom field container for a particular EAV type (users, manufacturers, orders, etc)
 *
 * @package application.model.eav
 * @author Integry Systems <http://integry.com>
 */
class EavFieldManager implements iEavFieldManager
{
	private $classID;
	private $stringIdentifier;

	public function __construct($classID)
	{
		if (!is_numeric($classID))
		{
			$newID = EavField::getClassID($classID);
			if (!$newID)
			{
				$this->stringIdentifier = $classID;
			}

			$classID = $newID;
		}

		$this->classID = $classID;
	}

	public function getClassID()
	{
		return $this->classID;
	}

	public function getClassName()
	{
		return EavField::getClassNameById($this->classID);
	}

	public function getSpecFieldsWithGroupsArray()
	{
		$groups = ActiveRecordModel::getRecordSetArray('EavFieldGroup', $this->getGroupFilter());
		$fields = ActiveRecordModel::getRecordSetArray('EavField', $this->getFieldFilter(), array('EavFieldGroup'));
		return ActiveRecordGroup::mergeGroupsWithFields('EavFieldGroup', $groups, $fields);
	}

	public function setValidation(RequestValidator $validator)
	{
		EavSpecificationManagerCommon::setValidation($validator);
	}

	public function getSpecificationFieldSet()
	{
		return ActiveRecordModel::getRecordSet('EavField', $this->getFieldFilter(), array('EavFieldGroup'));
	}

	public static function getClassFieldSet($class)
	{
		return ActiveRecordModel::getRecordSet('EavField', self::getFieldFilter($class), array('EavFieldGroup'));
	}

	/**
	 * Creates a select filter for custom fields
	 *
	 * @param bool $includeParentFields
	 * @return ARSelectFilter
	 */
	private function getFieldFilter($class = null)
	{
		$classID = $class ? EavField::getClassID($class) : $this->classID;

		$filter = new ARSelectFilter(new EqualsCond(new ARFieldHandle('EavField', 'classID'), $classID));
		if (is_null($class) && $this->stringIdentifier)
		{
			$filter->mergeCondition(new EqualsCond(new ARFieldHandle('EavField', 'stringIdentifier'), $this->stringIdentifier));
		}

		$filter->setOrder(new ARFieldHandle('EavFieldGroup', 'position'));
		$filter->setOrder(new ARFieldHandle('EavField', 'position'));

		return $filter;
	}

	/**
	 * Creates a select filter for fields groups
	 * @return ARSelectFilter
	 */
	private function getGroupFilter()
	{
		$filter = new ARSelectFilter(new EqualsCond(new ARFieldHandle('EavFieldGroup', 'classID'), $this->classID));
		if ($this->stringIdentifier)
		{
			$filter->mergeCondition(new EqualsCond(new ARFieldHandle('EavFieldGroup', 'stringIdentifier'), $this->stringIdentifier));
		}

		$filter->setOrder(new ARFieldHandle('EavFieldGroup', 'position'));

		return $filter;
	}
}

?>
