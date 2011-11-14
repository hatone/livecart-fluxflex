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

ClassLoader::import("application.model.ObjectFile");
ClassLoader::import("application.model.product.Product");
ClassLoader::import("application.model.product.ProductFileGroup");

/**
 * Defines a file that is assigned to a particular product. This is mostly needed for
 * tangible (downloadable) products. Multiple files can be assigned to one product and
 * related files can be grouped together using ProductFileGroup, which is useful if there
 * are many files assigned to the same product.
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductFile extends ObjectFile
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = parent::defineSchema($className);
		$schema->registerField(new ARForeignKeyField("productID", "Product", "ID", "Product", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("productFileGroupID", "ProductFileGroup", "ID", "ProductFileGroup", ARInteger::instance()));
		$schema->registerField(new ARField("isPublic", ARBool::instance()));
		$schema->registerField(new ARField("isEmbedded", ARBool::instance()));
		$schema->registerField(new ARField("title", ARArray::instance()));
		$schema->registerField(new ARField("description", ARArray::instance()));
		$schema->registerField(new ARField("position", ARInteger::instance()));
		$schema->registerField(new ARField("allowDownloadDays", ARInteger::instance()));
		$schema->registerField(new ARField("allowDownloadCount", ARInteger::instance()));
	}

	/**
	 * Create new instance of product file
	 *
	 * @param Product $product Product to which the file belongs
	 * @param string $filePath Path to that file (possibly a temporary file)
	 * @param string $fileName File name with extension. (image.jpg)
	 * @return ActiveRecord
	 */
	public static function getNewInstance(Product $product, $filePath, $fileName, $pathOrUrl = null)
	{
		$productFileInstance = parent::getNewInstance(__CLASS__, $filePath, $fileName, $pathOrUrl);
		$productFileInstance->product->set($product);

		return $productFileInstance;
	}

	/**
	 * Gets an existing ProductFile record
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * return ActiveRecord
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords, $data);
	}

	/**
	 * Loads a set of ProductFile instances
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
	 *
	 * @param Product $product
	 *
	 * @return ARSet
	 */
	public static function getFilesByProduct(Product $product)
	{
		return self::getRecordSet(self::getFilesByProductFilter($product), array('ProductFileGroup'));
	}

	public static function getOrderFiles(ARSelectFilter $f)
	{
		$f->mergeCondition(new EqualsCond(new ARFieldHandle('CustomerOrder', 'isCancelled'), 0));
		$f->mergeCondition(new EqualsCond(new ARFieldHandle('CustomerOrder', 'isFinalized'), true));
		$f->mergeCondition(new EqualsCond(new ARFieldHandle('CustomerOrder', 'isPaid'), true));
		//$f->mergeCondition(new EqualsCond(new ARFieldHandle('Product', 'type'), Product::TYPE_DOWNLOADABLE));
		$f->setOrder(new ARFieldHandle('CustomerOrder', 'ID'), 'DESC');

		$downloadable = ActiveRecordModel::getRecordSet('OrderedItem', $f, array('Product', 'CustomerOrder'));
		$fileArray = array();
		foreach ($downloadable as &$item)
		{
			$files = $item->getProduct()->getFiles();
			$itemFiles = array();
			foreach ($files as $file)
			{
				if ($item->isDownloadable($file))
				{
					$itemFiles[] = $file->toArray();
				}
			}

			if (!$itemFiles)
			{
				continue;
			}

			$array = $item->toArray();
			$array['Product']['Files'] = ProductFileGroup::mergeGroupsWithFields($item->getProduct()->getFileGroups()->toArray(), $itemFiles);

			foreach ($array['Product']['Files'] as $key => $file)
			{
				if (!isset($file['ID']))
				{
					unset($array['Product']['Files'][$key]);
				}
			}

			$fileArray[] = $array;
		}

		return $fileArray;
	}

	private static function getFilesByProductFilter(Product $product)
	{
		$filter = new ARSelectFilter();
		$filter->joinTable('ProductFileGroup', 'ProductFile', 'ID', 'productFileGroupID');

		$filter->setOrder(new ARFieldHandle("ProductFileGroup", "position"), ARSelectFilter::ORDER_ASC);
		$filter->setCondition(new EqualsCond(new ARFieldHandle(__CLASS__, 'productID'), $product->getID()));
		$filter->setOrder(new ARFieldHandle(__CLASS__, 'position'), ARSelectFilter::ORDER_ASC);

		return $filter;
	}
}

?>
