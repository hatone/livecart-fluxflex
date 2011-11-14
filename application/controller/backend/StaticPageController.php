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
ClassLoader::import("application.model.staticpage.StaticPage");

/**
 * Static page management
 *
 * @package application.controller.backend
 * @author	Integry Systems
 * @role page
 */
class StaticPageController extends StoreManagementController
{
	/**
	 *	Main settings page
	 */
	public function index()
	{
		$f = new ARSelectFilter();
		$f->setOrder(new ARFieldHandle('StaticPage', 'position'));
		$s = ActiveRecordModel::getRecordSetArray('StaticPage', $f);

		$pages = array();
		foreach ($s as $page)
		{
			$pages[$page['ID']] = array('title' => $page['title_lang'], 'parent' => $page['parentID']);
		}

		$response = new ActionResponse();
		$response->set('pages', json_encode($pages));
		return $response;
	}

	/**
	 * @role create
	 */
	public function add()
	{
		$response = new ActionResponse();
		$response->set('form', $this->getForm());
		return $response;
	}

	public function edit()
	{
		$page = StaticPage::getInstanceById($this->request->get('id'), StaticPage::LOAD_DATA);
		$pageArray = $page->toArray();
		$spec = $page->getSpecification();
		$form = $this->getForm();
		$form->setData($pageArray);
		$response = new ActionResponse();
		$spec->setFormResponse($response, $form);
		$response->set('form', $form);
		$response->set('page', $pageArray);

		return $response;
	}

	/**
	 * Reorder pages
	 *
	 * @role sort
	 */
	public function reorder()
	{
		$inst = StaticPage::getInstanceById($this->request->get('id'), StaticPage::LOAD_DATA);

		$f = new ARSelectFilter();
		$handle = new ARFieldHandle('StaticPage', 'position');
		if ('down' == $this->request->get('order'))
		{
			$f->setCondition(new MoreThanCond($handle, $inst->position->get()));
			$f->setOrder($handle, 'ASC');
		}
		else
		{
			$f->setCondition(new LessThanCond($handle, $inst->position->get()));
			$f->setOrder($handle, 'DESC');
		}
		$f->setLimit(1);

		$s = ActiveRecordModel::getRecordSet('StaticPage', $f);

		if ($s->size())
		{
			$pos = $inst->position->get();
			$replace = $s->get(0);
			$inst->position->set($replace->position->get());
			$replace->position->set($pos);
			$inst->save();
			$replace->save();

			return new JSONResponse(array('id' => $inst->getID(), 'order' => $this->request->get('order')), 'success');
		}
		else
		{
			return new JSONResponse(false, 'failure', $this->translate('_could_not_reorder_pages'));
		}
	}

	/**
	 * @role update
	 */
	public function update()
	{
		$page = StaticPage::getInstanceById((int)$this->request->get('id'), StaticPage::LOAD_DATA);
		return $this->save($page);
	}

	/**
	 * @role update
	 */
	public function move()
	{
		$page = StaticPage::getInstanceById((int)$this->request->get('id'), StaticPage::LOAD_DATA);

		if ($this->request->get('parent'))
		{
			$parent = StaticPage::getInstanceById((int)$this->request->get('parent'), StaticPage::LOAD_DATA);
		}
		else
		{
			$parent = null;
		}

		$page->parent->set($parent);

		return $this->save($page, /*do not update menu*/false);
	}

	/**
	 * @role create
	 */
	public function create()
	{
		$page = StaticPage::getNewInstance();

		return $this->save($page);
	}

	/**
	 * @role remove
	 */
	public function delete()
	{
		try
		{
			$inst = StaticPage::getInstanceById($this->request->get('id'), StaticPage::LOAD_DATA);
			$inst->delete();
			return new JSONResponse(array('id' => $inst->getID()), 'success');
		}
		catch (Exception $e)
		{
			return new JSONResponse(false, 'failure');
		}
	}

	public function emptyPage()
	{
		return new ActionResponse();
	}

	private function save(StaticPage $page, $updateMenu = true)
	{
		$request = $this->getRequest();
		$page->getSpecification();
		$page->loadRequestData($request);

		if($updateMenu)
		{
			$menu = array (
				'INFORMATION' => !!$request->get('menuInformation'),
				'ROOT_CATEGORIES' => !!$request->get('menuRootCategories')
			);
			if($menu['INFORMATION'] == false && $menu['ROOT_CATEGORIES'] == false)
			{
				$menu = null;
			}
			$page->menu->set($menu);
		}
		
		$page->save();
		$arr = $page->toArray();
		return new JSONResponse(array('id' => $page->getID(), 'title' => $arr['title_lang']), 'success', $this->translate('_page_has_been_successfully_saved'));
	}

	private function getForm()
	{
		return new Form($this->buildValidator());
	}

	private function buildValidator()
	{
		ClassLoader::import('application.helper.filter.HandleFilter');

		$val = $this->getValidator('staticPage', $this->request);
		$val->addCheck('title', new IsNotEmptyCheck($this->translate('_err_title_empty')));
		$val->addCheck('text', new IsNotEmptyCheck($this->translate('_err_text_empty')));
		$val->addFilter('handle', HandleFilter::create());

		return $val;
	}
}

?>
