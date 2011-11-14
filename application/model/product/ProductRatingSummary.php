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
ClassLoader::import('application.model.category.ProductRatingType');
ClassLoader::import('application.model.product.Product');

/**
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductRatingSummary extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField('ID', ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField('productID', 'Product', 'ID', null, ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField('ratingTypeID', 'ProductRatingType', 'ID', null, ARInteger::instance()));
		$schema->registerField(new ArField('ratingSum', ARInteger::instance()));
		$schema->registerField(new ArField('ratingCount', ARInteger::instance()));
		$schema->registerField(new ArField('rating', ARFloat::instance(8)));
	}

	/*####################  Static method implementations ####################*/

	public static function getNewInstance(Product $product, ProductRatingType $type = null)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->product->set($product);
		$instance->ratingType->set($type);
		return $instance;
	}

	public static function getInstance(Product $product, ProductRatingType $type = null)
	{
		$field = new ARFieldHandle(__CLASS__, 'ratingTypeID');
		$summary = $product->getRelatedRecordSet(__CLASS__, new ARSelectFilter($type ? new EqualsCond($field, $type->getID()) : new IsNullCond($field)));
		if ($summary->size())
		{
			return $summary->get(0);
		}
		else
		{
			return self::getNewInstance($product, $type);
		}
	}

	public static function getProductRatingsArray(Product $product)
	{
		$f = new ARSelectFilter();
		$f->setOrder(new ARFieldHandle('ProductRatingType', 'position'));
		return $product->getRelatedRecordSetArray('ProductRatingSummary', $f, array('ProductRatingType'));
	}
}

?>
