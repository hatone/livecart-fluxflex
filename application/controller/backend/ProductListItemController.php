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

ClassLoader::import("application.controller.backend.abstract.StoreManagementController");
ClassLoader::import("application.model.category.Category");
ClassLoader::import("application.model.category.ProductList");
ClassLoader::import("application.model.product.Product");

/**
 * Manage category product list items
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role product
 */
class ProductListItemController extends StoreManagementController
{
	/**
	 * Creates new relationship
	 *
	 * @role update
	 */
	public function add()
	{
		$productID = $this->request->get('relatedownerID');
		$ownerID = $this->request->get('id');

		$list = ActiveRecordModel::getInstanceById('ProductList', $ownerID, ActiveRecordModel::LOAD_DATA);
		$product = Product::getInstanceByID($productID, Product::LOAD_DATA, array('ProductImage'));

		if(!$list->contains($product))
		{
			$list->addProduct($product);

			$response = new ActionResponse();
			$response->set('product', $product->toArray());
			$response->set('added', true);
			return $response;
		}
		else
		{
			return new JSONResponse(array('error' => $this->translate('_err_multiple')));
		}
	}

	/**
	 * @role update
	 */
	public function delete()
	{
		$item = ActiveRecordModel::getInstanceByID('ProductListItem', $this->request->get('id'), ActiveRecordModel::LOAD_DATA);
		$item->delete();

		return new JSONResponse(false, 'success');
	}

	/**
	 * @role update
	 */
	public function sort()
	{
		$target = $this->request->get('target');
		preg_match('/_(\d+)$/', $target, $match); // Get group.

		foreach($this->request->get($this->request->get('target'), array()) as $position => $id)
		{
			$item = ActiveRecordModel::getInstanceByID('ProductListItem', $id);
			$item->position->set($position);

			if (isset($match[1]))
			{
				$item->productList->set(ActiveRecordModel::getInstanceById('ProductList', $match[1]));
			}

			$item->save();
		}

		return new JSONResponse(false, 'success');
	}
}

?>
