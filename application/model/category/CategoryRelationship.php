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
ClassLoader::import('application.model.category.Category');

/**
 *
 * @package application.model.category
 * @author Integry Systems <http://integry.com>
 */
class CategoryRelationship extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField('ID', ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField('relatedCategoryID', 'Category', 'ID', null, ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField('categoryID', 'Category', 'ID', null, ARInteger::instance()));
		$schema->registerField(new ARField('position', ARInteger::instance()));
	}

	/*####################  Static method implementations ####################*/

	public static function getNewInstance(Category $category, Category $relatedCategory)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->category->set($category);
		$instance->relatedCategory->set($relatedCategory);
		return $instance;
	}

	/*####################  Saving ####################*/

	protected function insert()
	{
		$this->setLastPosition('category');

		return parent::insert();
	}
}

?>
