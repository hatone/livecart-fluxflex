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
 * Custom EAV field group controller
 *
 * @package application.controller.backend.abstract.eav
 * @author	Integry Systems
 */
abstract class EavFieldGroupControllerCommon extends StoreManagementController
{
	/**
	 * Array of available language codes. First language code is default language
	 *
	 * @var array
	 */
	protected $languageCodes = array();

	protected abstract function getClassName();

	protected abstract function getParent($id);

	/**
	 * Get specification field group data
	 *
	 * @return JSONResponse
	 */
	public function item()
	{
		return new JSONResponse($this->getInstanceByID($this->request->get('id'), true)->toArray());
	}

	public function update()
	{
		return $this->save($this->getInstanceByID($this->request->get('id')));
	}

	public function create()
	{
		$specFieldGroup = call_user_func_array(array($this->getClassName(), 'getNewInstance'), array($this->getParent($this->request->get('categoryID'))));
		return $this->save($specFieldGroup);
	}

	/**
	 * Delete field group from database
	 *
	 * @return JSONResponse Status
	 */
	public function delete()
	{
		if($id = $this->request->get("id", false))
		{
			ActiveRecordModel::deleteById($this->getClassName(), $id);
			return new JSONResponse(false, 'success');
		}
		else
		{
			return new JSONResponse(false, 'failure', $this->translate('_could_not_remove_specfield_group'));
		}
	}

	/**
	 * Sort field groups
	 *
	 * @return JSONResponse Status
	 */
	public function sort()
	{
		foreach($this->request->get($this->request->get('target'), array()) as $position => $key)
		{
			// Except new fields, because they are not yet in database
			$group = $this->getInstanceByID($key);
			$group->setFieldValue('position', $position);
			$group->save();
		}

		return new JSONResponse(false, 'success');
	}

	/**
	 * Create array of language codes
	 *
	 * @see self::$languageCodes
	 */
	private function createLanguageCodes()
	{
		if(!empty($this->languageCodes)) return true;

		$this->languageCodes[] = $this->application->getDefaultLanguageCode();
		foreach ($this->application->getLanguageList()->toArray() as $lang)
		{
			if($lang['isDefault'] != 1) $this->languageCodes[] = $lang['ID'];
		}
	}

	/**
	 * Validate submitted specification group
	 *
	 * @param unknown_type $values
	 * @param unknown_type $config
	 * @return unknown
	 */
	private function validate($values = array(), $languageCodes)
	{
		$errors = array();

		if(!isset($values["name_{$languageCodes[0]}"]) || $values["name_{$languageCodes[0]}"] == '')
		{
			$errors["name_{$languageCodes[0]}"] = '_error_you_should_provide_default_group_name';
		}

		return $errors;
	}

	/**
	 * Save group data to the database
	 *
	 * @return JSONResponse Returns status and errors if status is equal to failure
	 */
	protected function save(EavFieldGroupCommon $specFieldGroup)
	{
		$this->createLanguageCodes();
		if(count($errors = $this->validate($this->request->getValueArray(array("name_{$this->languageCodes[0]}")), $this->languageCodes)) == 0)
		{
			foreach($this->application->getLanguageArray(true) as $langCode)
			{
				$specFieldGroup->setValueByLang('name', $langCode, $this->request->get('name_' . $langCode));
			}

			$specFieldGroup->save();

			return new JSONResponse(array('id' => $specFieldGroup->getID()), 'success');
		}
		else
		{
			return new JSONResponse(array('errors' => $this->translateArray($errors)), 'failure', $this->translate('_could_not_save_specification_group'));
		}
	}

	private function getInstanceByID($id, $loadData = false)
	{
		return call_user_func_array(array($this->getClassName(), 'getInstanceById'), array($id, $loadData));
	}
}

?>
