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

ClassLoader::import('application.controller.backend.abstract.eav.EavFieldControllerCommon');
ClassLoader::import('application.model.category.Category');
ClassLoader::import('application.model.category.SpecField');
ClassLoader::import('library.*');

/**
 * Category specification field ("extra field") controller
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role category
 */
class SpecFieldController extends EavFieldControllerCommon
{
	protected function getParent($id)
	{
		return Category::getInstanceByID($id);
	}

	protected function getFieldClass()
	{
		return 'SpecField';
	}

	public function index()
	{
		return parent::index();
	}

	/**
	 * Displays form for creating a new or editing existing one product group specification field
	 *
	 * @return ActionResponse
	 */
	public function item()
	{
		$specFieldList = parent::item()->getValue();

		$specFieldList['categoryID'] = $specFieldList['Category']['ID'];
		unset($specFieldList['Category']);
		unset($specFieldList['SpecFieldGroup']['Category']);

		return new JSONResponse($specFieldList);
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
	public function create()
	{
		return parent::create();
	}

	protected function save(SpecField $specField)
	{
		return parent::save($specField);
	}

	/**
	 * Delete specification field from database
	 *
	 * @role update
	 * @return JSONResponse
	 */
	public function delete()
	{
		return parent::delete();
	}

	/**
	 * Sort specification fields
	 *
	 * @role update
	 * @return JSONResponse
	 */
	public function sort()
	{
		return parent::sort();
	}
}

?>
