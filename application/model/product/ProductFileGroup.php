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
 * Related ProductFiles can be grouped together using ProductFileGroup, which is useful if there
 * are many files assigned to the same product.
 * 
 * @package application.model.product
 * @author Integry Systems <http://integry.com>   
 */
class ProductFileGroup extends ProductParametersGroup
{
	private static $nextPosition = false;
	
	public static function defineSchema($className = __CLASS__)
	{
		$schema = parent::defineSchema($className);
		$schema->setName("ProductFileGroup");

		$schema->registerField(new ARField("name", ARArray::instance()));
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
	 * @return ProductFileGroup
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
	 * @return ProductFileGroup
	 */
	public static function getNewInstance(Product $product)
	{
		$group = parent::getNewInstance(__CLASS__);
		$group->product->set($product);

		return $group;
	}
	
	/**
	 * @return ARSet
	 */
	public static function getProductGroups(Product $product)
	{
		return self::getRecordSet(self::getProductGroupsFilter($product), !ActiveRecord::LOAD_REFERENCES);
	}
	
	private static function getProductGroupsFilter(Product $product)
	{
		$filter = new ARSelectFilter();

		$filter->setOrder(new ARFieldHandle(__CLASS__, "position"), 'ASC');
		$filter->setCondition(new EqualsCond(new ARFieldHandle(__CLASS__, "productID"), $product->getID()));
		
		return $filter;
	}
	
	public static function mergeGroupsWithFields($groups, $fields)
	{
		return parent::mergeGroupsWithFields(__CLASS__, $groups, $fields);
	}

	private function getFilesFilter()
	{
		$filter = new ARSelectFilter();

		$filter->setCondition(new EqualsCond(new ARFieldHandle('ProductFile', "productFileGroupID"), $this->getID()));
		
		return $filter;
	}
	
	public function getFiles($loadReferencedRecords = false) 
	{
		return ProductFile::getRecordSet($this->getFilesFilter(), $loadReferencedRecords);
	}
	
	public function delete()
	{
		foreach($this->getFiles() as $productFile) 
		{
			$productFile->deleteFile();
		}
		
		return parent::delete();
	}
}

?>
