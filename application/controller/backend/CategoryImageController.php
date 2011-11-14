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

ClassLoader::import("application.controller.backend.abstract.ObjectImageController");
ClassLoader::import('application.model.category.Category');
ClassLoader::import("application.model.category.CategoryImage");

/**
 * Product Category Image controller
 *
 * @package application.controller.backend
 * @author Integry Systems
 *
 * @role category
 */
class CategoryImageController extends ObjectImageController
{
	public function index()
	{
		return parent::index();
	}

	/**
	 * @role update
	 */
	public function upload()
	{
		return parent::upload();
	}

	/**
	 * @role update
	 */
	public function save()
	{
		return parent::save();
	}

	public function resizeImages()
	{
		return parent::resizeImages();
	}

	/**
	 * @role update
	 */
	public function delete()
	{
		if(parent::delete())
		{
			return new JSONResponse(false);
		}
		else
		{
			return new JSONResponse(false, 'failure', $this->translate('_could_not_remove_category_image'));
		}
	}

	/**
	 * @role sort
	 */
	public function saveOrder()
	{
		parent::saveOrder();

		return new JSONResponse(true);
	}

	protected function getModelClass()
	{
		return 'CategoryImage';
	}

	protected function getOwnerClass()
	{
		return 'Category';
	}

	protected function getForeignKeyName()
	{
		return 'categoryID';
	}

}

?>
