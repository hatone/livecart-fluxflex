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

ClassLoader::import('application.controller.FrontendController');
ClassLoader::import('application.model.searchable.SearchableModel');

/**
 * Search for non-products
 *
 * @author Integry Systems
 * @package application.controller
 */
class SearchController extends FrontendController
{
	public function index()
	{
		$this->loadLanguageFile('Category');

		$inst = SearchableModel::getInstanceByModelClass($this->request->get('type'));
		if (!$inst)
		{
			return null;
		}

		$query = $this->request->get('q');
		$f = $inst->getSelectFilter($query);

		$perPage = $this->config->get('SEARCH_MODEL_PER_PAGE');
		$page = $this->request->get('page', 1);
		$f->setLimit($perPage, $perPage * ($page - 1));

		$response = new ActionResponse();
		$response->set('results', $this->fetchData($inst, $f));
		$response->set('page', $page);
		$response->set('query', $query);
		$response->set('perPage', $perPage);
		$response->set('url', $this->router->createUrl(array('controller' => 'search', 'action' => 'index', 'query' => array('type' => $inst->getClassName(), 'q' => $query, 'page' => '0'))));
		return $response;
	}

	public function searchAll($searchTerm)
	{
		$res = array();

		foreach (SearchableModel::getInstances(SearchableModel::FRONTEND_SEARCH_MODEL) as $searchable)
		{
			$f = $searchable->getSelectFilter($searchTerm);
			$f->setLimit($this->config->get('SEARCH_MODEL_PREVIEW'));
			$res[$searchable->getClassName()] = $this->fetchData($searchable, $f);
		}

		return $res;
	}

	private function fetchData(SearchableModel $searchable, ARSelectFilter $filter)
	{
		$class = $searchable->getClassName();
		$ret = array();
		$ret['records'] = ActiveRecordModel::getRecordSetArray($class, $filter);
		$ret['count'] = ActiveRecordModel::getRecordCount($class, $filter);
		$ret['meta'] = $searchable->toArray();

		return $ret;
	}
}

?>
