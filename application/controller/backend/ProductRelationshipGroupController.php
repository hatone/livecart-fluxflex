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

ClassLoader::import("application.controller.backend.abstract.ProductListControllerCommon");
ClassLoader::import("application.model.category.Category");
ClassLoader::import("application.model.product.Product");
ClassLoader::import("application.model.product.ProductRelationshipGroup");

/**
 * Controller for handling product based actions performed by store administrators
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role product
 */
class ProductRelationshipGroupController extends ProductListControllerCommon
{
	protected function getOwnerClassName()
	{
		return 'Product';
	}

	protected function getGroupClassName()
	{
		return 'ProductRelationshipGroup';
	}

	/**
	 * @role update
	 */
	public function create()
	{
		$group = ProductRelationshipGroup::getNewInstance($this->getOwnerInstanceByID($this->request->get('ownerID')), $this->request->get('type'));
		return $this->save($group);
	}

	/**
	 * @role update
	 */
	public function update()
	{
		return parent::update();
	}

	/**
	 * @role update
	 */
	public function delete()
	{
		return parent::delete();
	}

	/**
	 * @role update
	 */
	public function sort()
	{
		return parent::sort();
	}

	public function edit()
	{
		return parent::edit();
	}
}

?>
