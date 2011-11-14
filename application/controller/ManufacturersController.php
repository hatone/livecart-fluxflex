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
ClassLoader::import('application.model.product.Manufacturer');
ClassLoader::import('application.model.product.ProductFilter');
ClassLoader::import('application.model.filter.ManufacturerFilter');
ClassLoader::import('application.model.category.Category');

/**
 * Manufacturer list
 *
 * @author Integry Systems
 * @package application.controller
 */
class ManufacturersController extends FrontendController
{
	public function index()
	{
		// get filter to select manufacturers of active products only
		$rootCat = Category::getRootNode();
		$f = new ARSelectFilter();
		$productFilter = new ProductFilter($rootCat, $f);

		$ids = $counts = array();
		foreach (ActiveRecordModel::getDataBySQL('SELECT DISTINCT(manufacturerID), COUNT(*) AS cnt FROM Product ' . $f->createString() . ' GROUP BY manufacturerID') as $row)
		{
			$ids[] = $row['manufacturerID'];
			$counts[$row['manufacturerID']] = $row['cnt'];
		}

		$f = new ARSelectFilter(new InCond(new ARFieldHandle('Manufacturer', 'ID'), $ids));
		$f->mergeCondition(new NotEqualsCond(new ARFieldHandle('Manufacturer', 'name'), ''));
		$f->setOrder(new ARFieldHandle('Manufacturer', 'name'));
		$manufacturers = ActiveRecordModel::getRecordSetArray('Manufacturer', $f);

		foreach ($manufacturers as &$manufacturer)
		{
			$manufacturer['url'] = $this->getManufacturerFilterUrl($manufacturer);
		}

		$this->addBreadCrumb($this->translate('_manufacturers'), '');

		$response = new ActionResponse();
		$response->setReference('manufacturers', $manufacturers);
		$response->set('counts', $counts);
		$response->set('rootCat', $rootCat->toArray());
		return $response;
	}

	public function view()
	{
		$manufacturer = Manufacturer::getInstanceByID($this->request->get('id'), true);
		$manufacturer->load();
		return new RedirectResponse($this->getManufacturerFilterUrl($manufacturer->toArray()));
	}

	private function getManufacturerFilterUrl($manufacturerArray)
	{
		static $templateUrl;

		if (!$templateUrl)
		{
			$templateUrl = $this->getFilterUrlTemplate();
		}

		return strtr($templateUrl, array('#' => $manufacturerArray['ID'], '|' => createHandleString($manufacturerArray['name'])));
	}

	private function getFilterUrlTemplate()
	{
		include_once(ClassLoader::getRealPath('application.helper.smarty') . '/function.categoryUrl.php');
		$params = array('filters' => array(new ManufacturerFilter(999, '___')), 'data' => Category::getRootNode()->toArray());
		$templateUrl = createCategoryUrl($params, $this->application);
		return strtr($templateUrl, array(999 => '#', '___' => '|'));
	}
}
