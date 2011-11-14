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

ClassLoader::import('application.controller.backend.abstract.StoreManagementController');
ClassLoader::import('application.controller.backend.abstract.ActiveGridController');
ClassLoader::import('application.model.category.Category');
ClassLoader::import('application.model.filter.FilterGroup');
ClassLoader::import('application.model.product.Product');
ClassLoader::import('application.model.product.ProductSpecification');
ClassLoader::import('application.helper.ActiveGrid');
ClassLoader::import('application.helper.massAction.MassActionInterface');

/**
 * Controller for handling product based actions performed by store administrators
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role product
 */
class ProductCategoryController extends StoreManagementController
{
	public function index()
	{
		$product = Product::getInstanceById($this->request->get('id'), ActiveRecord::LOAD_DATA, array('Category'));
		$product->category->get()->getPathNodeSet();

		$additional = $product->getAdditionalCategories();
		foreach ($additional as $category)
		{
			$category->getPathNodeSet();
		}

		$response = new ActionResponse('product', $product->toArray());

		if ($additional)
		{
			$response->set('categories', ARSet::buildFromArray($additional)->toArray());
		}

		return $response;
	}

	public function saveMainCategory()
	{
		$product = Product::getInstanceByID($this->request->get('id'), ActiveRecord::LOAD_DATA, array('Category'));
		$category = Category::getInstanceByID($this->request->get('categoryId'), ActiveRecord::LOAD_DATA);
		$product->category->set($category);
		$product->save();

		Category::recalculateProductsCount();

		return new RawResponse($category->getID());
	}

	public function addCategory()
	{
		$product = Product::getInstanceByID($this->request->get('id'), ActiveRecord::LOAD_DATA, array('Category'));
		$category = Category::getInstanceByID($this->request->get('categoryId'), ActiveRecord::LOAD_DATA);

		// check if the product is not assigned to this category already
		$relation = ActiveRecordModel::getInstanceByIdIfExists('ProductCategory', array('productID' => $product->getID(), 'categoryID' => $category->getID()));
		if ($relation->isExistingRecord() || ($product->category->get() === $category))
		{
			return new JSONResponse(false, 'failure', $this->translate('_err_already_assigned'));
		}

		$relation->save();

		return new JSONResponse(array('data' => $relation->toFlatArray()));
	}

	public function delete()
	{
		$product = Product::getInstanceByID($this->request->get('id'), ActiveRecord::LOAD_DATA, array('Category'));
		$category = Category::getInstanceByID($this->request->get('categoryId'), ActiveRecord::LOAD_DATA);

		$relation = ActiveRecordModel::getInstanceById('ProductCategory', array('productID' => $product->getID(), 'categoryID' => $category->getID()));
		$relation->delete();

		return new JSONResponse(array('data' => $relation->toFlatArray()));
	}
}
