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

ClassLoader::import("application.model.system.ActiveRecordGroup");
ClassLoader::import("application.model.system.MultilingualObject");

/**
 * A generic class for grouping assigned product entities (files, related products, etc.)
 * 
 * @package application.model.product
 * @author Integry Systems <http://integry.com>   
 */
abstract class ProductParametersGroup extends MultilingualObject
{
	private static $nextPosition = false;
	
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("productID", "Product", "ID", null, ARInteger::instance()));
		$schema->registerField(new ARField("position", ARInteger::instance()));
		
		return $schema;
	}

	public static function mergeGroupsWithFields($className, $groups, $fields)
	{
		return ActiveRecordGroup::mergeGroupsWithFields($className, $groups, $fields);
	}
	
	public function setNextPosition()
	{
		$className = get_class($this);
		
		if(!is_integer(self::$nextPosition))
		{
			$filter = new ARSelectFilter();
			$filter->setCondition(new EqualsCond(new ARFieldHandle($className, 'productID'), $this->product->get()->getID()));
			$filter->setOrder(new ARFieldHandle($className, 'position'), ARSelectFilter::ORDER_DESC);
			$filter->setLimit(1);
			
			self::$nextPosition = 0;
			foreach(ActiveRecord::getRecordSet($className, $filter) as $relatedProductGroup) 
			{
				self::$nextPosition = $relatedProductGroup->position->get();
			}
		}
		
		$this->position->set(++self::$nextPosition);
	}
	
	public function save($forceOperation = false)
	{
		if(!$this->isExistingRecord()) $this->setNextPosition();
		
		parent::save($forceOperation);
	}
}

?>
