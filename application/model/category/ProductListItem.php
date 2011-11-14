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
ClassLoader::import('application.model.category.ProductList');
ClassLoader::import('application.model.product.Product');

/**
 *
 * @package application.model.category
 * @author Integry Systems <http://integry.com>
 */
class ProductListItem extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField('ID', ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField('productListID', 'ProductList', 'ID', null, ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField('productID', 'Product', 'ID', null, ARInteger::instance()));
		$schema->registerField(new ARField('position', ARInteger::instance()));
		$schema->registerAutoReference('productID');
	}

	/*####################  Static method implementations ####################*/

	public static function getNewInstance(ProductList $productList, Product $product)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->productList->set($productList);
		$instance->product->set($product);
		return $instance;
	}

	/*####################  Saving ####################*/

	protected function insert()
	{
		$this->setLastPosition('productList');

		return parent::insert();
	}
}

?>
