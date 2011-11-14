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

/**
 * Category specification field value controller
 *
 * @package application.controller.abstract.eav
 * @author	Integry Systems
 */
abstract class EavFieldValueControllerCommon extends StoreManagementController
{
	protected abstract function getClassName();

	/**
	 * Delete field value from database
	 *
	 * @return JSONResponse Indicates status
	 */
	public function delete()
	{
		if($id = $this->request->get('id', false))
		{
			ActiveRecordModel::deleteById($this->getClassName(), $id);
			return new JSONResponse(false, 'success');
		}
		else
		{
			return new JSONResponse(false, 'failure');
		}
	}

	/**
	 * Sort specification field values
	 *
	 * return JSONResponse Indicates status
	 */
	public function sort()
	{
		foreach($this->request->get($this->request->get('target'), array()) as $position => $key)
		{
			// Except new fields, because they are not yet in database
			if(!empty($key) && !preg_match('/^new/', $key))
			{
				$specField = $this->getInstanceByID($key);
				$specField->setFieldValue('position', $position);
				$specField->save();
			}
		}

		return new JSONResponse(false, 'success');
	}

	public function mergeValues()
	{
		$mergedIntoValue = $this->getInstanceByID($this->request->get('mergeIntoValue'), true);

		foreach($this->request->get('mergedValues') as $mergedValueId)
		{
			$mergedValue = $this->getInstanceByID($mergedValueId, true);
			$mergedIntoValue->mergeWith($mergedValue);
		}

		$mergedIntoValue->save();
		return new JSONResponse(array('status' => 'success'));
	}

	private function getInstanceByID($id)
	{
		return call_user_func_array(array($this->getClassName(), 'getInstanceById'), array($id, ActiveRecordModel::LOAD_DATA));
	}
}

?>
