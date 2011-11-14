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

ClassLoader::import("application.model.product.ProductParametersGroup");

/**
 * Groups related products. This is useful when there are several different related products assigned
 * to one product, so similar products could be grouped together.
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductRelationshipGroup extends ProductParametersGroup
{
	private static $nextPosition = false;

	const TYPE_CROSS = 0;
	const TYPE_UP = 1;

	public static function defineSchema($className = __CLASS__)
	{
		$schema = parent::defineSchema($className);
		$schema->setName("ProductRelationshipGroup");

		$schema->registerField(new ARField("name", ARArray::instance()));
		$schema->registerField(new ARField("type", ARInteger::instance()));
	}

	/**
	 * Load related products group record set
	 *
	 * @param ARSelectFilter $filter
	 * @param bool $loadReferencedRecords
	 *
	 * @return ARSet
	 */
	public static function getRecordSet(ARSelectFilter $filter, $loadReferencedRecords = false)
	{
		return parent::getRecordSet(__CLASS__, $filter, $loadReferencedRecords);
	}

	/**
	 * Get related products group active record by ID
	 *
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 *
	 * @return ProductRelationshipGroup
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false)
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords);
	}

	/**
	 * Creates a new related products group
	 *
	 * @param Product $product
	 *
	 * @return ProductRelationshipGroup
	 */
	public static function getNewInstance(Product $product, $type)
	{
		$group = parent::getNewInstance(__CLASS__);
		$group->product->set($product);
		$group->type->set($type);

		return $group;
	}

	/**
	 * @return ARSet
	 */
	public static function getProductGroups(Product $product, $type)
	{
		return self::getRecordSet(self::getProductGroupsFilter($product, $type), !ActiveRecord::LOAD_REFERENCES);
	}

	public static function getProductGroupArray(Product $product, $type)
	{
		return parent::getRecordSetArray(__CLASS__, self::getProductGroupsFilter($product, $type), !ActiveRecord::LOAD_REFERENCES);
	}

	private static function getProductGroupsFilter(Product $product, $type)
	{
		$filter = new ARSelectFilter();
		$filter->setOrder(new ARFieldHandle("ProductRelationshipGroup", "position"), 'ASC');
		$filter->setCondition(new EqualsCond(new ARFieldHandle("ProductRelationshipGroup", "productID"), $product->getID()));
		$filter->mergeCondition(new EqualsCond(new ARFieldHandle("ProductRelationshipGroup", "type"), $type));

		return $filter;
	}

	public static function mergeGroupsWithFields($groups, $fields)
	{
		return parent::mergeGroupsWithFields(__CLASS__, $groups, $fields);
	}
}

?>
