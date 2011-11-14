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

/**
 *
 * @package application.model.category
 * @author Integry Systems <http://integry.com>
 */
class ProductList extends MultilingualObject
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField('ID', ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField('categoryID', 'Category', 'ID', null, ARInteger::instance()));
		$schema->registerField(new ARField('isRandomOrder', ARBool::instance()));
		$schema->registerField(new ARField('name', ARArray::instance()));
		$schema->registerField(new ARField('listStyle', ARInteger::instance()));
		$schema->registerField(new ARField('limitCount', ARInteger::instance()));
		$schema->registerField(new ARField('position', ARInteger::instance()));
	}

	/*####################  Static method implementations ####################*/

	public static function getNewInstance(Category $category)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->category->set($category);
		return $instance;
	}

	public static function getCategoryLists(Category $category)
	{
		$f = new ARSelectFilter();
		$f->setOrder(new ARFieldHandle(__CLASS__, 'position'));
		return $category->getRelatedRecordSet(__CLASS__, $f);
	}

	/*####################  Saving ####################*/

	protected function insert()
	{
		$this->setLastPosition('category');

		return parent::insert();
	}

	public function addProduct(Product $product)
	{
		$item = ProductListItem::getNewInstance($this, $product);
		$item->save();
		return $item;
	}

	public function contains(Product $product)
	{
		$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle('ProductListItem', 'productID'), $product->getID()));
		return $this->getRelatedRecordCount('ProductListItem', $f) > 0;
	}
}

?>
