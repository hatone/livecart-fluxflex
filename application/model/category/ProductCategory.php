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
ClassLoader::import('application.model.product.Product');

/**
 * Assigns a product to additional category
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductCategory extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryForeignKeyField('categoryID', 'Category', 'ID', 'Category', ARInteger::instance()));
		$schema->registerField(new ARPrimaryForeignKeyField('productID', 'Product', 'ID', 'Product', ARInteger::instance()));
	}

	/*####################  Static method implementations ####################*/

	/**
	 * Creates a new related product
	 *
	 * @param Product $product
	 * @param Category $category
	 *
	 * @return ProductCategory
	 */
	public static function getNewInstance(Product $product, Category $category)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->product->set($product);
		$instance->category->set($category);

		return $instance;
	}

	/*####################  Saving ####################*/

	public function delete()
	{
		$this->product->get()->updateCategoryCounters($this->product->get()->getCountUpdateFilter(true), $this->category->get());
		parent::delete();
	}

	protected function insert()
	{
		$this->product->get()->updateCategoryCounters($this->product->get()->getCountUpdateFilter(), $this->category->get());
		$this->product->get()->registerAdditionalCategory($this->category->get());
		$insertResult = parent::insert();
		Category::updateCategoryIntervals($this->product->get());
		return $insertResult;
	}

}

?>
