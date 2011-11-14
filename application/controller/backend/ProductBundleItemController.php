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
ClassLoader::import("application.model.product.Product");
ClassLoader::import("application.model.product.ProductBundle");

/**
 * Manage category product list items
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role product
 */
class ProductBundleItemController extends StoreManagementController
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

		$owner = Product::getInstanceByID($ownerID, Product::LOAD_DATA);
		$product = Product::getInstanceByID($productID, Product::LOAD_DATA, array('ProductImage'));

		if ($product->isBundle())
		{
			return new JSONResponse(array('error' => $this->translate('_err_bundle_with_bundle')));
		}

		if(!ProductBundle::hasRelationship($owner, $product))
		{
			$instance = ProductBundle::getNewInstance($owner, $product);
			if (!$instance)
			{
				return new JSONResponse(array('error' => $this->translate('_err_add_to_itself')));
			}

			$instance->save();

			$response = new ActionResponse();
			$response->set('product', $product->toArray());
			$response->set('added', true);
			$response->set('total', $this->getTotal($owner));
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
		$relatedProductID = (int)$this->request->get('id');
		$productID = (int)$this->request->get('relatedProductID');

		$item = ActiveRecordModel::getInstanceByID('ProductBundle', array('productID' => $productID, 'relatedProductID' => $relatedProductID), ActiveRecordModel::LOAD_DATA);
		$item->delete();

		return new JSONResponse(array('total' => $this->getTotal($item->getProduct())), 'success');
	}

	/**
	 * @role update
	 */
	public function setCount()
	{
		$productID = (int)$this->request->get('id');
		$relatedProductID = (int)$this->request->get('relatedownerID');

		$item = ActiveRecordModel::getInstanceByID('ProductBundle', array('productID' => $productID, 'relatedProductID' => $relatedProductID), ActiveRecordModel::LOAD_DATA);

		$count = $this->request->get('count');
		if ($count < 0 || !$count)
		{
			$count = 1;
		}
		$item->count->set($count);
		$item->save();

		return new JSONResponse(array('count' => $count, 'total' => $this->getTotal($item->getProduct())), 'success');
	}

	/**
	 * @role update
	 */
	public function sort()
	{
		$target = $this->request->get('target');
		preg_match('/_(\d+)$/', $target, $match); // Get group.
		$productID = $match[1];

		foreach($this->request->get($this->request->get('target'), array()) as $position => $id)
		{
			$item = ActiveRecordModel::getInstanceByID('ProductBundle', array('productID' => $productID, 'relatedProductID' => $id), ActiveRecord::LOAD_DATA);
			$item->position->set($position);
			$item->save();
		}

		return new JSONResponse(false, 'success');
	}

	private function getTotal(Product $product)
	{
		$currency = $this->application->getDefaultCurrency();
		return $currency->getFormattedPrice(ProductBundle::getTotalBundlePrice($product, $currency));
	}
}

?>
