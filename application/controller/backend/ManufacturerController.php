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

ClassLoader::import("application.controller.backend.abstract.ActiveGridController");
ClassLoader::import("application.model.product.Manufacturer");

/**
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role product
 */
class ManufacturerController extends ActiveGridController
{
	public function index()
	{
		return $this->getGridResponse();
	}

	public function add()
	{
		$manufacturer = Manufacturer::getNewInstance('test');

		$response = new ActionResponse();
		$form = $this->buildForm($manufacturer);
		$manufacturer->getSpecification()->setFormResponse($response, $form);
		$response->set('form', $form);

		return $response;
	}

	public function edit()
	{
		$manufacturer = ActiveRecordModel::getInstanceById('Manufacturer', $this->request->get('id'), Manufacturer::LOAD_DATA, Manufacturer::LOAD_REFERENCES);
		$manufacturer->getSpecification();

		$response = new ActionResponse('manufacturer', $manufacturer->toArray());
		$form = $this->buildForm($manufacturer);
		$form->setData($manufacturer->toArray());

		$manufacturer->getSpecification()->setFormResponse($response, $form);
		$response->set('form', $form);

		return $response;
	}

	public function create()
	{
		return $this->save(Manufacturer::getNewInstance(''));
	}

	public function update()
	{
		return $this->save(ActiveRecordModel::getInstanceById('Manufacturer', $this->request->get('id'), Manufacturer::LOAD_DATA, Manufacturer::LOAD_REFERENCES));
	}

	protected function save(Manufacturer $manufacturer)
	{
		$validator = $this->buildValidator($manufacturer);

		if ($validator->isValid())
		{
			$manufacturer->loadRequestData($this->request);
			$manufacturer->save();
			return new JSONResponse(array('manufacturer' => $manufacturer->toFlatArray()), 'success', $this->translate('_manufacturer_was_successfully_saved'));
		}
		else
		{
			return new JSONResponse(array('errors' => $validator->getErrorList()), 'failure', $this->translate('_could_not_save_manufacturer'));
		}
	}

	public function changeColumns()
	{
		parent::changeColumns();
		return $this->getGridResponse();
	}

	public function selectPopup()
	{
		return $this->index();
	}

	private function getGridResponse()
	{
		$this->loadLanguageFile('backend/Product');

		$response = new ActionResponse();
		$this->setGridResponse($response);
		return $response;
	}

	protected function getClassName()
	{
		return 'Manufacturer';
	}

	protected function getCSVFileName()
	{
		return 'manufacturers.csv';
	}

	protected function getDefaultColumns()
	{
		return array('Manufacturer.ID', 'Manufacturer.name');
	}

	protected function setDefaultSortOrder(ARSelectFilter $filter)
	{
		$filter->setOrder(new ARFieldHandle($this->getClassName(), 'name'), 'ASC');
	}

	public function autoComplete()
	{
	  	$f = new ARSelectFilter();
	  	$c = new LikeCond(new ARFieldHandle('Manufacturer', 'name'), $this->request->get('manufacturer') . '%');
	  	$f->setCondition($c);

	  	$results = ActiveRecordModel::getRecordSetArray('Manufacturer', $f);

		$resp = array();
	  	foreach ($results as $value)
	  	{
			$resp[$value['ID']] = $value['name'];
		}

		return new AutoCompleteResponse($resp);
	}

	private function buildValidator(Manufacturer $manufacturer)
	{
		$validator = $this->getValidator("manufacturer", $this->request);
		$validator->addCheck("name", new IsNotEmptyCheck($this->translate("_manufacturer_name_empty")));

		$manufacturer->getSpecification()->setValidation($validator);

		return $validator;
	}

	/**
	 * Builds a category form instance
	 *
	 * @return Form
	 */
	private function buildForm(Manufacturer $manufacturer)
	{
		return new Form($this->buildValidator($manufacturer));
	}
}

?>
