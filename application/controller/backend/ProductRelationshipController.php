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
ClassLoader::import("application.model.product.Product");

/**
 * Manage related products
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role product
 */
class ProductRelationshipController extends StoreManagementController
{
	public function index()
	{
		$productID = (int)$this->request->get('id');
		$product = Product::getInstanceByID($productID, ActiveRecord::LOAD_DATA, array('Category'));

		$response = new ActionResponse();
		$response->set('categoryID', $product->category->get()->getID());
		$response->set('productID', $productID);
		$response->set('relationships', $product->getRelationships($this->request->get('type'))->toArray());
		$response->set('relationshipsWithGroups', $product->getRelatedProductsWithGroupsArray($this->request->get('type')));
		$response->set('type', $this->request->get('type'));

		return $response;
	}

	/**
	 * Products popup
	 *
	 * @role update
	 */
	public function selectProduct()
	{
		$response = new ActionResponse();

		$categoryList = Category::getRootNode()->getDirectChildNodes();
		$categoryList->unshift(Category::getRootNode());
		$response->set("categoryList", $categoryList->toArray());

		return $response;
	}

	/**
	 * Creates new relationship
	 *
	 * @role update
	 */
	public function addRelated()
	{
		$productID = (int)$this->request->get('id');
		$relatedProductID = (int)$this->request->get('relatedownerID');

		$relatedProduct = Product::getInstanceByID($relatedProductID, true, array('DefaultImage' => 'ProductImage'), Product::LOAD_DATA);
		$product = Product::getInstanceByID($productID, Product::LOAD_DATA);

		if(!$relatedProduct->isRelatedTo($product, $this->request->get('type')))
		{
			try
			{
				$product->addRelatedProduct($relatedProduct, $this->request->get('type'));
				$product->save();

				$response = new ActionResponse();
				$response->set('product', $relatedProduct->toArray());
				$response->set('added', true);
				return $response;
			}
			catch(ProductRelationshipException $e)
			{
				$error = '_err_circular';
			}
		}
		else
		{
			$error = '_err_multiple';
		}

		return new JSONResponse(array('error' => $this->translate($error)));
	}

	/**
	 * @role update
	 */
	public function delete()
	{
		$productID = (int)$this->request->get('id');
		$relatedProductID = (int)$this->request->get('relatedownerID');

		$relatedProduct = Product::getInstanceByID($relatedProductID);
		$product = Product::getInstanceByID($productID);

		$product->removeFromRelatedProducts($relatedProduct, $this->request->get('type'));
		$product->save();

		return new JSONResponse(false, 'success');
	}

	/**
	 * @role update
	 */
	public function sort()
	{
		$target = $this->request->get('target');

		if (!$target)
		{
			foreach ($this->request->toArray() as $key => $value)
			{
				if (is_array($value))
				{
					$target = $key;
					break;
				}
			}
		}

		$product = Product::getInstanceByID($this->request->get('id'));

		$type = $this->request->get('type');

		preg_match('/_(\d+)$/', $target, $match); // Get group.
		if (substr($target, 0, 8) == 'noGroup_')
		{
			$match = array();
		}

		foreach($this->request->get($target, array()) as $position => $key)
		{
			if(empty($key)) continue;

			$relationship = ProductRelationship::getInstance($product, Product::getInstanceByID((int)$key), $type);
			$relationship->position->set((int)$position);

			if(isset($match[1]))
			{
				$relationship->productRelationshipGroup->set(ProductRelationshipGroup::getInstanceByID((int)$match[1]));
			}
			else
			{
				$relationship->productRelationshipGroup->setNull();
			}

			$relationship->save();
		}

		return new JSONResponse(false, 'success');
	}
}

?>
