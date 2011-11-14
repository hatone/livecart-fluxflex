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
ClassLoader::import('application.model.sitenews.NewsPost');

/**
 * @package application.controller.backend
 * @author Integry Systems
 * @role news
 */
class SiteNewsController extends StoreManagementController
{
	public function index()
	{
		$f = new ARSelectFilter();
		$f->setOrder(new ARFieldHandle('NewsPost', 'position'), 'DESC');
		$response = new ActionResponse('newsList', ActiveRecordModel::getRecordSetArray('NewsPost', $f));
		$response->set('form', $this->buildForm());
		return $response;
	}

	/**
	 * @role update
	 */
	public function edit()
	{
		$form = $this->buildForm();
		$form->loadData(NewsPost::getInstanceById($this->request->get('id'), NewsPost::LOAD_DATA)->toArray());
		return new ActionResponse('form', $form);
	}

	/**
	 * @role update
	 */
	public function save()
	{
		$validator = $this->buildValidator();
		if (!$validator->isValid())
		{
			return new JSONResponse(array('err' => $validator->getErrorList()));
		}

		$post = $this->request->get('id') ? ActiveRecordModel::getInstanceById('NewsPost', $this->request->get('id'), ActiveRecordModel::LOAD_DATA) : ActiveRecordModel::getNewInstance('NewsPost');
		$post->loadRequestData($this->request);
		$post->save();

		return new JSONResponse($post->toArray());
	}

	/**
	 * Create new record
	 * @role create
	 */
	public function add()
	{
		return $this->save();
	}

	/**
	 * Remove a news entry
	 *
	 * @role delete
	 * @return JSONResponse
	 */
	public function delete()
	{
		try
	  	{
			ActiveRecordModel::deleteById('NewsPost', $this->request->get('id'));
			return new JSONResponse(false, 'success');
		}
		catch (Exception $exc)
		{
			return new JSONResponse(false, 'failure', $this->translate('_could_not_remove_news'));
		}
	}

	/**
	 * Save news entry order
	 * @role sort
	 * @return RawResponse
	 */
	public function saveOrder()
	{
	  	$order = array_reverse($this->request->get('newsList'));

		foreach ($order as $key => $value)
		{
			$update = new ARUpdateFilter();
			$update->setCondition(new EqualsCond(new ARFieldHandle('NewsPost', 'ID'), $value));
			$update->addModifier('position', $key);
			ActiveRecord::updateRecordSet('NewsPost', $update);
		}

		$resp = new RawResponse();
	  	$resp->setContent($this->request->get('draggedId'));
		return $resp;
	}

	/**
	 * @role status
	 * @return JSONResponse
	 */
	public function setEnabled()
	{
		$post = ActiveRecordModel::getInstanceById('NewsPost', $this->request->get('id'), NewsPost::LOAD_DATA);
		$post->isEnabled->set($this->request->get("status"));
		$post->save();

		return new JSONResponse($post->toArray());
	}

	private function buildForm()
	{
		return new Form($this->buildValidator());
	}

	private function buildValidator()
	{
		$validator = $this->getValidator("newspost", $this->request);
		$validator->addCheck('text', new IsNotEmptyCheck($this->translate('_err_enter_text')));

		return $validator;
	}
}

?>
