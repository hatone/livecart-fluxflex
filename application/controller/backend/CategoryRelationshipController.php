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
ClassLoader::import('application.model.category.Category');
ClassLoader::import('application.model.category.CategoryRelationship');

/**
 * Controller for handling category based actions performed by store administrators
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role category
 */
class CategoryRelationshipController extends StoreManagementController
{
	public function index()
	{
		$category = Category::getInstanceById($this->request->get('id'), ActiveRecord::LOAD_DATA);

		$f = select();
		$f->setOrder(f('CategoryRelationship.position'));
		$additional = $category->getRelatedRecordSet('CategoryRelationship', $f, array('Category_RelatedCategory'));
		$categories = array();
		foreach ($additional as $cat)
		{
			$categories[] = $cat;
			$cat->relatedCategory->get()->load();
			$cat->relatedCategory->get()->getPathNodeSet();
		}

		$response = new ActionResponse('category', $category->toArray());
		$response->set('categories', ARSet::buildFromArray($categories)->toArray());

		return $response;
	}

	public function addCategory()
	{
		$category = Category::getInstanceByID($this->request->get('id'), ActiveRecord::LOAD_DATA, array('Category'));
		$relatedCategory = Category::getInstanceByID($this->request->get('categoryId'), ActiveRecord::LOAD_DATA);

		// check if the category is not assigned to this category already
		$f = select(eq('CategoryRelationship.relatedCategoryID', $relatedCategory->getID()));
		if ($category->getRelatedRecordSet('CategoryRelationship', $f)->size())
		{
			return new JSONResponse(false, 'failure', $this->translate('_err_already_assigned'));
		}

		$relation = CategoryRelationship::getNewInstance($category, $relatedCategory);
		$relation->save();

		$relatedCategory->getPathNodeSet();
		return new JSONResponse(array('data' => $relation->toFlatArray()));
	}

	public function saveOrder()
	{
	  	$order = $this->request->get('relatedCategories_' . $this->request->get('id'));
		foreach ($order as $key => $value)
		{
			$update = new ARUpdateFilter();
			$update->setCondition(new EqualsCond(new ARFieldHandle('CategoryRelationship', 'ID'), $value));
			$update->addModifier('position', $key);
			ActiveRecord::updateRecordSet('CategoryRelationship', $update);
		}

		return new JSONResponse(false, 'success');
	}

	public function delete()
	{
		$relation = ActiveRecordModel::getInstanceById('CategoryRelationship', $this->request->get('categoryId'));
		$relation->delete();

		return new JSONResponse(array('data' => $relation->toFlatArray()));
	}
}
