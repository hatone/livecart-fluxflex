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

ClassLoader::import('application.model.order.OrderedItem');
ClassLoader::import('application.model.product.ProductFile');

/**
 *
 *
 * @package application.model.order
 * @author Integry Systems <http://integry.com>
 */
class OrderedFile extends ActiveRecordModel
{
	/**
	 * Define database schema used by this active record instance
	 *
	 * @param string $className Schema name
	 */
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("orderedItemID", "OrderedItem", "ID", "OrderedItem", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("productFileID", "ProductFile", "ID", "ProductFile", ARInteger::instance()));
		$schema->registerField(new ARField("timesDownloaded", ARInteger::instance()));
		$schema->registerField(new ARField("lastDownloadTime", ARDateTime::instance()));
	}

	/*####################  Static method implementations ####################*/

	public static function getNewInstance(OrderedItem $item, ProductFile $file)
	{
		$instance = parent::getNewInstance(__CLASS__);
		$instance->orderedItem->set($item);
		$instance->productFile->set($file);
		return $instance;
	}

	public static function getInstance(OrderedItem $item, ProductFile $file)
	{
		$instance = $item->getRelatedRecordSet('OrderedFile', select(eq('OrderedFile.productFileID', $file->getID())))->shift();
		if (!$instance)
		{
			$instance = self::getNewInstance($item, $file);
		}

		return $instance;
	}

	public function registerDownload()
	{
		$this->timesDownloaded->set($this->timesDownloaded->get() + 1);
		$this->save();
	}
}

?>
