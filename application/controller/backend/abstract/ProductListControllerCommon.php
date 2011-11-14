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

/**
 * Product list management - related products, category lists, etc.
 *
 * @package application.controller.backend.abstract
 * @author Integry Systems
 * @role product
 */
abstract class ProductListControllerCommon extends StoreManagementController
{
	protected abstract function getOwnerClassName();

	protected abstract function getGroupClassName();

	protected function getOwnerInstanceByID($id)
	{
		return ActiveRecordModel::getInstanceByID($this->getOwnerClassName(), $id, ActiveRecordModel::LOAD_DATA);
	}

	protected function getGroupInstanceByID($id, $loadData = true)
	{
		return ActiveRecordModel::getInstanceByID($this->getGroupClassName(), $id, $loadData);
	}

	/**
	 * @role update
	 */
	public function create()
	{
		$group = call_user_func(array($this->getGroupClassName(), 'getNewInstance'), $this->getOwnerInstanceByID($this->request->get('ownerID')));
		return $this->save($group);
	}

	/**
	 * @role update
	 */
	public function update()
	{
		return $this->save($this->getGroupInstanceByID($this->request->get('ID')));
	}

	/**
	 * @role update
	 */
	public function delete()
	{
		$this->getGroupInstanceByID($this->request->get('id'))->delete();
		return new JSONResponse(false, 'success');
	}

	/**
	 * @role update
	 */
	public function sort()
	{
		foreach($this->request->get($this->request->get('target'), array()) as $position => $key)
		{
			if(empty($key)) continue;
			$relationship = $this->getGroupInstanceByID($key, false);
			$relationship->position->set((int)$position);
			$relationship->save();
		}

		return new JSONResponse(false, 'success');
	}

	public function edit()
	{
		return new JSONResponse($this->getGroupInstanceByID($this->request->get('id'))->toArray());
	}

	protected function buildValidator()
	{
		$validator = $this->getValidator(get_class($this) . "Validator", $this->request);

		$validator->addCheck('name', new IsNotEmptyCheck($this->translate('_err_relationship_name_is_empty')));

		return $validator;
	}

	protected function save(ActiveRecordModel $listGroup)
	{
		$validator = $this->buildValidator();
		if ($validator->isValid())
		{
			$listGroup->loadRequestData($this->request);
			$listGroup->save();

			return new JSONResponse(array('ID' => $listGroup->getID(), 'data' => $listGroup->toArray()), 'success');
		}
		else
		{
			return new JSONResponse(array('errors' => $validator->getErrorList()), 'failure');
		}
	}
}

?>
