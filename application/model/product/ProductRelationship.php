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

/**
 * Assigns a related (recommended) product to a particular product
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductRelationship extends ActiveRecordModel
{
	const TYPE_CROSS = 0;
	const TYPE_UP = 1;

	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName("ProductRelationship");

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("productID", "Product", "ID", "Product", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("relatedProductID", "Product", "ID", "Product", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("productRelationshipGroupID", "ProductRelationshipGroup", "ID", "ProductRelationshipGroup", ARInteger::instance()));
		$schema->registerField(new ARField("position",  ARInteger::instance()));
		$schema->registerField(new ARField("type",  ARInteger::instance()));
	}

	/*####################  Static method implementations ####################*/

	/**
	 * Get related product active record by ID
	 *
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 *
	 * @return ProductRelationshipGroup
	 */
	public static function getInstance(Product $product, Product $relatedProduct, $type, $loadRecordData = false, $loadReferencedRecords = false)
	{
		$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle(__CLASS__, 'productID'), $product->getID()));
		$f->mergeCondition(new EqualsCond(new ARFieldHandle(__CLASS__, 'relatedProductID'), $relatedProduct->getID()));
		$f->mergeCondition(new EqualsCond(new ARFieldHandle(__CLASS__, 'type'), $type));

		$set = parent::getRecordSet(__CLASS__, $f, $loadReferencedRecords);
		return $set->size() ? $set->get(0) : null;
	}

	/**
	 * Creates a new related product
	 *
	 * @param Product $product
	 * @param Product $relatedProduct
	 *
	 * @return ProductRelationship
	 */
	public static function getNewInstance(Product $product, Product $related, ProductRelationshipGroup $group = null)
	{
		if(null == $product || null == $related || $product === $related || $product->getID() == $related->getID())
		{
			require_once('ProductRelationshipException.php');
			throw new ProductRelationshipException('Expected two different products when creating a relationship');
		}

		$relationship = parent::getNewInstance(__CLASS__);

		$relationship->product->set($product);
		$relationship->relatedProduct->set($related);
		if(!is_null($group))
		{
			$relationship->productRelationshipGroup->set($group);
		}

		return $relationship;
	}

	/**
	 * Get relationships set
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
	 * Gets an existing relationship instance
	 *
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * @return ProductRelationship
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords, $data);
	}

	/*####################  Value retrieval and manipulation ####################*/

	public static function hasRelationship(Product $product, Product $relatedToProduct, $type)
	{
		return self::getInstance($product, $relatedToProduct, $type) instanceof ProductRelationship;
	}

	/*####################  Saving ####################*/

	protected function insert()
	{
	  	// get max position
	  	$f = self::getRelatedProductsSetFilter($this->product->get(), $this->type->get());
	  	$f->setLimit(1);
	  	$rec = ActiveRecord::getRecordSetArray('ProductRelationship', $f);
		$position = (is_array($rec) && count($rec) > 0) ? $rec[0]['position'] + 1 : 0;
		$this->position->set($position);

		return parent::insert();
	}

	/*####################  Get related objects ####################*/

	/**
	 * Get product relationships
	 *
	 * @param Product $product
	 * @return ARSet
	 */
	public static function getRelationships(Product $product, $loadReferencedRecords = array('RelatedProduct' => 'Product'), $type)
	{
		return self::getRecordSet(self::getRelatedProductsSetFilter($product, $type), $loadReferencedRecords);
	}

	/**
	 * Get product relationships
	 *
	 * @param Product $product
	 * @return array
	 */
	public static function getRelationshipsArray(Product $product, $loadReferencedRecords = array('RelatedProduct' => 'Product'), $type)
	{
		return parent::getRecordSetArray(__CLASS__, self::getRelatedProductsSetFilter($product, $type), $loadReferencedRecords);
	}

	private static function getRelatedProductsSetFilter(Product $product, $type)
	{
		$filter = new ARSelectFilter();

		$filter->joinTable('ProductRelationshipGroup', 'ProductRelationship', 'ID', 'productRelationshipGroupID');
		$filter->setOrder(new ARFieldHandle("ProductRelationshipGroup", "position"), 'ASC');
		$filter->setOrder(new ARFieldHandle(__CLASS__, "position"), 'ASC');
		$filter->setCondition(new EqualsCond(new ARFieldHandle(__CLASS__, "productID"), $product->getID()));
		$filter->mergeCOndition(new EqualsCond(new ARFieldHandle(__CLASS__, "type"), $type));

		return $filter;
	}
}

?>
