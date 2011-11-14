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

ClassLoader::import('application.model.ObjectImage');

/**
 * Category image (icon)
 *
 * @package application.model.category
 * @author Integry Systems
 */
class CategoryImage extends ObjectImage
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = parent::defineSchema($className);
		$schema->registerField(new ARForeignKeyField("categoryID", "Category", "ID", "Category", ARInteger::instance()));
	}

	/*####################  Static method implementations ####################*/

	public static function getNewInstance(Category $category)
	{
	  	$catImage = ActiveRecord::getNewInstance(__CLASS__);
	  	$catImage->category->set($category);
	  	return $catImage;
	}

	/*####################  Value retrieval and manipulation ####################*/

	public function getPath($size = 0)
	{
		if (!$this->isLoaded)
		{
			$this->load();
		}

	  	return self::getImagePath($this->getID(), $this->category->get()->getID(), $size);
	}

	public static function getImageSizes()
	{
		$config = self::getApplication()->getConfig();

		$sizes = array();
		$k = 0;
		while ($config->isValueSet('IMG_C_W_' . ++$k))
		{
			$sizes[$k] = array($config->get('IMG_C_W_' . $k), $config->get('IMG_C_H_' . $k));
		}

		return $sizes;
	}

	protected static function getImagePath($imageID, $productID, $size)
	{
		return self::getImageRoot(__CLASS__) . $productID. '-' . $imageID . '-' . $size . '.jpg';
	}

	/*####################  Saving ####################*/

	public static function deleteByID($id)
	{
		parent::deleteByID(__CLASS__, $id, 'categoryID');
	}

	protected function insert()
	{
		return parent::insert('categoryID');
	}

	public static function transformArray($array, ARSchema $schema)
	{
		return parent::transformArray($array, $schema, 'Category', 'categoryID');
	}

	/*####################  Get related objects ####################*/

	public function getOwner()
	{
		return $this->category->get();
	}
}

?>
