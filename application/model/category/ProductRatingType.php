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

ClassLoader::import('application.model.system.MultilingualObject');
ClassLoader::import('application.model.category.Category');
ClassLoader::import('application.model.user.User');

/**
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductRatingType extends MultilingualObject
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField('ID', ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField('categoryID', 'Category', 'ID', null, ARInteger::instance()));
		$schema->registerField(new ARField('name', ARArray::instance()));
		$schema->registerField(new ARField('position', ARInteger::instance()));
	}

	/*####################  Static method implementations ####################*/

	public static function getNewInstance(Category $category)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->category->set($category);
		return $instance;
	}

	public static function getCategoryRatingTypes(Category $category)
	{
		$f = new ARSelectFilter();
		$f->setOrder(new ARFieldHandle(__CLASS__, 'position'));
		return $category->getRelatedRecordSet(__CLASS__, $f);
	}

	public static function getProductRatingTypes(Product $product)
	{
		$types = self::getRecordSet(__CLASS__, self::getRatingTypeFilter($product), array('Category'));
		if (!$types->size())
		{
			$types = self::getDefaultRatingTypeSet();
		}

		return $types;
	}

	public static function getProductRatingTypeArray(Product $product)
	{
		$types = self::getRecordSetArray(__CLASS__, self::getRatingTypeFilter($product), array('Category'));
		if (!$types)
		{
			$types = self::getDefaultRatingTypeSet()->toArray();
		}

		return $types;
	}

	public static function getDefaultRatingType()
	{
		return self::getNewInstance(Category::getRootNode());
	}

	private function getDefaultRatingTypeSet()
	{
		$set = new ARSet();
		$set->add(self::getDefaultRatingType());
		return $set;
	}

	private function getRatingTypeFilter(Product $product)
	{
		$path = $product->getCategory()->getPathNodeArray(Category::INCLUDE_ROOT_NODE);

		$filter = new ARSelectFilter();

		$filter->setOrder(new ARFieldHandle("Category", "lft"), 'ASC');
		$filter->setOrder(new ARFieldHandle(__CLASS__, "position"), 'ASC');

		$cond = new EqualsCond(new ARFieldHandle(__CLASS__, "categoryID"), $product->getCategory()->getID());

		foreach ($path as $node)
		{
			$cond->addOR(new EqualsCond(new ARFieldHandle(__CLASS__, "categoryID"), $node['ID']));
		}

		$filter->setCondition($cond);

		return $filter;
	}

	/*####################  Saving ####################*/

	public function getCategory()
	{
		return $this->category->get();
	}

	protected function insert()
	{
		// get max position
	  	$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle(__CLASS__, 'categoryID'), $this->getCategory()->getID()));
	  	$f->setOrder(new ARFieldHandle(get_class($this), 'position'), 'DESC');
	  	$f->setLimit(1);
	  	$rec = ActiveRecord::getRecordSetArray(get_class($this), $f);
		$position = (is_array($rec) && count($rec) > 0) ? $rec[0]['position'] + 1 : 1;

		$this->position->set($position);

		return parent::insert();
	}
}

?>
