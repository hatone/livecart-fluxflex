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
ClassLoader::import("application.model.eavcommon.EavFieldCommon");

/**
 * Category specification field ("extra field") controller
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role category
 */
abstract class EavFieldControllerCommon extends StoreManagementController
{
	protected abstract function getFieldClass();

	protected abstract function getParent($id);

	/**
	 * Configuration data
	 *
	 * @see self::getSpecFieldConfig
	 * @var array
	 */
	protected $specFieldConfig = array();

	/**
	 * Specification field index page
	 *
	 * @return ActionResponse
	 */
	public function index()
	{
		$categoryID = $this->request->get('id');

		$defaultSpecFieldValues = array
		(
			'ID' => $categoryID.'_new',
			'name' => array(),
			'description' => array(),
			'handle' => '',
			'values' => array(),
			'rootId' => 'specField_item_new_'.$categoryID.'_form',
			'type' => EavFieldCommon::TYPE_TEXT_SIMPLE,
			'dataType' => EavFieldCommon::DATATYPE_TEXT,
			'categoryID' => $categoryID,
			'isDisplayed' => true,
		);

		$response = new ActionResponse();
		$response->set('categoryID', $categoryID);
		$response->set('configuration', $this->getSpecFieldConfig());
		$response->set('specFieldsList', $defaultSpecFieldValues);
		$response->set('specFieldsWithGroups', $this->getParent($categoryID)->getSpecFieldsWithGroupsArray());

		return $response;
	}

	/**
	 * Displays form for creating a new or editing existing one product group specification field
	 *
	 * @return JSONResponse
	 */
	public function item()
	{
		$specFieldList = $this->getFieldInstanceByID($this->request->get('id'), true, true)->toArray(false, false);

		$valueClass = call_user_func(array(call_user_func(array($this->getFieldClass(), 'getSelectValueClass')), 'getValueClass'));
		$values = call_user_func_array(array($valueClass, 'getRecordSetArray'), array($specFieldList['ID']));
		foreach($values as $value)
		{
		   $specFieldList['values'][$value['ID']] = $value;
		}

		return new JSONResponse($specFieldList);
	}

	/**
	 * Delete specification field from database
	 *
	 * @return JSONResponse
	 */
	public function delete()
	{
		$id = $this->request->get("id", false);
		if(ActiveRecordModel::objectExists($this->getFieldClass(), $id))
		{
			ActiveRecordModel::deleteById($this->getFieldClass(), $id);
			return new JSONResponse(false, 'success');
		}
		else
		{
			return new JSONResponse(false, 'failure', $this->translate('_could_not_remove_attribute'));
		}
	}

	public function create()
	{
		$specField = call_user_func_array(array($this->getFieldClass(), 'getNewInstance'), array($this->getParent($this->request->get('categoryID'))));

		return $this->save($specField);
	}

	public function update()
	{
		if(ActiveRecordModel::objectExists($this->getFieldClass(), $this->request->get('ID')))
		{
			$specField = $this->getFieldInstanceByID($this->request->get('ID'));
		}
		else
		{
			return new JSONResponse(array(
					'errors' => array('ID' => $this->translate('_error_record_id_is_not_valid')),
					'ID' => (int)$this->request->get('ID')
				)/*,
				'failure',
				$this->translate('_could_not_save_attribute') */
			);
		}

		return $this->save($specField);
	}

	/**
	 * Sort specification fields
	 *
	 * @role update
	 * @return JSONResponse
	 */
	public function sort()
	{
		$target = $this->request->get('target');
		preg_match('/_(\d+)$/', $target, $match); // Get group.

		foreach($this->request->get($target, array()) as $position => $key)
		{
			if(!empty($key))
			{
				$specField = $this->getFieldInstanceByID($key);
				$specField->setFieldValue('position', $position);

				if(isset($match[1]))
				{
					$specField->getGroup()->set($this->getGroupInstanceByID($match[1])); // Change group
				}
				else
				{
					$specField->getGroup()->setNull();
				}

				$specField->save();
			}
		}

		return new JSONResponse(false, 'success');
	}

	/**
	 * Create and return configurational data. If configurational data is already created just return the array
	 *
	 * @see self::$specFieldConfig
	 * @return array
	 */
	protected function getSpecFieldConfig()
	{
		if(!empty($this->specFieldConfig)) return $this->specFieldConfig;

		$languages[$this->application->getDefaultLanguageCode()] =  $this->locale->info()->getOriginalLanguageName($this->application->getDefaultLanguageCode());
		foreach ($this->application->getLanguageSetArray() as $lang)
		{
			$languages[$lang['ID']] = $this->locale->info()->getOriginalLanguageName($lang['ID']);
		}

		$this->specFieldConfig = array(
			'languages' => $languages,
			'languageCodes' => array_keys($languages),
			'messages' => array
			(
				'deleteField' => $this->translate('_delete_field'),
				'removeFieldQuestion' => $this->translate('_remove_field_question')
			),

			'selectorValueTypes' => EavFieldCommon::getSelectorValueTypes(),
			'doNotTranslateTheseValueTypes' => array(2),
			'countNewValues' => 0
		);

		return $this->specFieldConfig;
	}

	protected function getFieldInstanceByID($id, $loadData = false, $loadReferencedRecords = false)
	{
		return call_user_func_array(array($this->getFieldClass(), 'getInstanceByID'), array($id, $loadData, $loadReferencedRecords));
	}

	protected function getGroupInstanceByID($id, $loadData = false, $loadReferencedRecords = false)
	{
		return call_user_func_array(array($this->getFieldClass() . 'Group', 'getInstanceByID'), array($id, $loadData, $loadReferencedRecords));
	}

	/**
	 * Creates a new or modifies an exisitng specification field (according to a passed parameters)
	 *
	 * @return JSONResponse Returns success status or failure status with array of erros
	 */
	protected function save(EavFieldCommon $specField)
	{
		$this->getSpecFieldConfig();
		$errors = $this->validate($this->request->getValueArray(array('handle', 'values', 'name', 'type', 'dataType', 'categoryID', 'ID')), $this->specFieldConfig['languageCodes']);

		if(!$errors)
		{
			$type = $this->request->get('advancedText') ? EavFieldCommon::TYPE_TEXT_ADVANCED : (int)$this->request->get('type');
			$dataType = EavFieldCommon::getDataTypeFromType($type);
			$categoryID = $this->request->get('categoryID');

			$values = $this->request->get('values');

			$specField->loadRequestData($this->request);

			foreach ($specField->getSchema()->getFieldsByType('ARBool') as $field)
			{
				$name = $field->getName();
				$specField->setFieldValue($name, $this->request->get($name, 0));
			}

			$specField->setFieldValue('dataType', $dataType);
			$specField->setFieldValue('type', $type);

			$specField->save();

			// save specification field values in database
			$newIDs = array();
			$valueClass = call_user_func(array(call_user_func(array($this->getFieldClass(), 'getSelectValueClass')), 'getValueClass'));
			if($specField->isSelector() && is_array($values))
			{
				$position = 1;
				$countValues = count($values);
				$i = 0;
				foreach ($values as $key => $value)
				{
					$i++;

					// If last new is empty miss it
					if($countValues == $i && preg_match('/new/', $key) && empty($value[$this->specFieldConfig['languageCodes'][0]]))
					{
						continue;
					}

					if(preg_match('/^new/', $key))
					{
						$specFieldValues = call_user_func_array(array($valueClass, 'getNewInstance'), array($specField));
					}
					else
					{
					   $specFieldValues = call_user_func_array(array($valueClass, 'getInstanceByID'), array($key));
					}

					if(EavFieldCommon::TYPE_NUMBERS_SELECTOR == $type)
					{
						$specFieldValues->setFieldValue('value', $value);
					}
					else
					{
						$specFieldValues->setLanguageField('value', $value, $this->specFieldConfig['languageCodes']);
					}

					$specFieldValues->setFieldValue('position', $position++);
					$specFieldValues->save();

	   				if(preg_match('/^new/', $key))
					{
						$newIDs[$specFieldValues->getID()] = $key;
					}
				}
			}

			return new JSONResponse(array('id' => $specField->getID(), 'newIDs' => $newIDs), 'success');
		}
		else
		{
			return new JSONResponse(array('errors' => $this->translateArray($errors)));
		}
	}

	/**
	 * Validates specification field form
	 *
	 * @param array $values List of values to validate.
	 * @param array $config
	 * @return array List of all errors
	 */
	protected function validate($values = array(), $languageCodes)
	{
		$errors = array();

		if(!isset($values['name']) || $values['name'] == '')
		{
			$errors['name'] = '_error_name_empty';
		}

		if(!isset($values['handle']) || $values['handle'] == '' || preg_match('/[^\w\d_.]/', $values['handle']))
		{
			$errors['handle'] = '_error_handle_invalid';
		}
		else
		{
			$values['ID'] = !isset($values['ID']) ? -1 : $values['ID'];
			$filter = new ARSelectFilter();
				$handleCond = new EqualsCond(new ARFieldHandle($this->getFieldClass(), 'handle'), $values['handle']);
				$handleCond->addAND(new EqualsCond(new ARFieldHandle($this->getFieldClass(), call_user_func(array($this->getFieldClass(), 'getOwnerIDColumnName'))), $values['categoryID']));
				$handleCond->addAND(new NotEqualsCond(new ARFieldHandle($this->getFieldClass(), 'ID'), $values['ID']));
			$filter->setCondition($handleCond);
			if(count(ActiveRecordModel::getRecordSetArray($this->getFieldClass(), $filter)) > 0)
			{
				$errors['handle'] =  '_error_handle_exists';
			}
		}

		if(!isset($values['handle']) || $values['handle'] == '')
		{
			$errors['handle'] = '_error_handle_empty';
		}

		if(in_array($values['type'], EavFieldCommon::getSelectorValueTypes()) && isset($values['values']) && is_array($values['values']))
		{
			$countValues = count($values['values']);
			$i = 0;
			foreach ($values['values'] as $key => $v)
			{
				$i++;
				if($countValues == $i && preg_match('/new/', $key) && empty($v[$languageCodes[0]]))
				{
					continue;
				}
				else if(!strlen($v[$languageCodes[0]]))
				{
					$errors["values[$key][{$languageCodes[0]}]"] = '_error_value_empty';
				}
				else if(EavFieldCommon::getDataTypeFromType($values['type']) == 2 && !is_numeric($v[$languageCodes[0]]))
				{
					$errors["values[$key][{$languageCodes[0]}]"] = '_error_value_is_not_a_number';
				}
			}
		}

		return $errors;
	}
}

?>
