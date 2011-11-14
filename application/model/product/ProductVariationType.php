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

ClassLoader::import('application.model.product.Product');
ClassLoader::import('application.model.system.MultilingualObject');
ClassLoader::import('application.model.product.ProductVariationTypeSet', true);

/**
 * Defines a product variation (parameter) type.
 *
 * This can be "size", "color", "weight", etc.
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductVariationType extends MultilingualObject
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("productID", "Product", "ID", null, ARInteger::instance()));

		$schema->registerField(new ARField("name", ARArray::instance()));
		$schema->registerField(new ARField("position", ARInteger::instance(4)));
	}

	public static function getNewInstance(Product $product)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->product->set($product);
		return $instance;
	}

	protected function insert()
	{
		if (is_null($this->position->get()))
		{
			$this->setLastPosition('product');
		}

		parent::insert();
	}
}

?>
