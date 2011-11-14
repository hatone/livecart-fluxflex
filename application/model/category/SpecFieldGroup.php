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

ClassLoader::import("application.model.category.SpecField");
ClassLoader::import("application.model.eavcommon.EavFieldGroupCommon");
ClassLoader::import("application.model.category.Category");

/**
 * SpecFieldGroups allow to group related attributes (SpecFields) together.
 *
 * @package application.model.category
 * @author Integry Systems <http://integry.com>
 */
class SpecFieldGroup extends EavFieldGroupCommon
{
	/**
	 * Define SpecFieldGroup database schema
	 */
	public static function defineSchema()
	{
		$schema = parent::defineSchema(__CLASS__);
		$schema->registerField(new ARForeignKeyField("categoryID", "Category", "ID", "Category", ARInteger::instance()));
	}

	/*####################  Static method implementations ####################*/

	/**
	 * Get new SpecFieldGroup active record instance
	 *
	 * @return SpecFieldGroup
	 */
	public static function getNewInstance(Category $category)
	{
		$inst = parent::getNewInstance(__CLASS__);
		$inst->category->set($category);

		return $inst;
	}

	/**
	 * Get specification group item instance
	 *
	 * @param int|array $recordID Record id
	 * @param bool $loadRecordData If true loads record's structure and data
	 * @param bool $loadReferencedRecords If true loads all referenced records
	 * @return SpecFieldGroup
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false)
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords);
	}

	public function getCategory()
	{
		return $this->category->get();
	}

	protected function getParentCondition()
	{
		return new EqualsCond(new ARFieldHandle(get_class($this), 'categoryID'), $this->getCategory()->getID());
	}
}

?>
