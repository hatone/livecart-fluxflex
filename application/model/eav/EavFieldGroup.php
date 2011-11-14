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

ClassLoader::import("application.model.eav.EavField");
ClassLoader::import("application.model.eavcommon.EavFieldGroupCommon");

/**
 * EavFieldGroups allow to group related attributes (EavFields) together.
 *
 * @package application.model.eav
 * @author Integry Systems <http://integry.com>
 */
class EavFieldGroup extends EavFieldGroupCommon
{
	public static function defineSchema()
	{
		$schema = parent::defineSchema(__CLASS__);
		$schema->registerField(new ARField("classID", ARInteger::instance()));
		$schema->registerField(new ARField("stringIdentifier", ARVarchar::instance(40)));
	}

	/*####################  Static method implementations ####################*/

	/**
	 * Get new EavFieldGroup active record instance
	 *
	 * @return EavFieldGroup
	 */
	public static function getNewInstance($className)
	{
		if ($className instanceof EavFieldManager)
		{
			$className = $className->getClassName();
		}

		$inst = parent::getNewInstance(__CLASS__);
		$inst->classID->set(EavField::getClassID($className));

		return $inst;
	}

	public function toArray()
	{
		$array = parent::toArray();
		$array['Category']['ID'] = $array['classID'];
		return $array;
	}

	/**
	 * Get specification group item instance
	 *
	 * @param int|array $recordID Record id
	 * @param bool $loadRecordData If true loads record's structure and data
	 * @param bool $loadReferencedRecords If true loads all referenced records
	 * @return EavFieldGroup
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false)
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords);
	}

	protected function getParentCondition()
	{
		return new EqualsCond(new ARFieldHandle(get_class($this), 'classID'), $this->classID->get());
	}
}

?>
