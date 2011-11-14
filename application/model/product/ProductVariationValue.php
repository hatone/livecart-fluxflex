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
ClassLoader::import('application.model.product.Product');
ClassLoader::import('application.model.product.ProductVariation');

/**
 * Relates a product variation to particular product.
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductVariationValue extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("productID", "Product", "ID", null, ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("variationID", "ProductVariation", "ID", null, ARInteger::instance()));
	}

	public static function getNewInstance(Product $product, ProductVariation $variation)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->product->set($product);
		$instance->variation->set($variation);
		return $instance;
	}
}

?>
