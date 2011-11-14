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
ClassLoader::import('application.model.eav.EavField');
ClassLoader::import('application.model.order.OfflineTransactionHandler');

/**
 * Manage custom EAV fields
 *
 * @package application.controller.backend
 * @author Integry Systems
 *
 */
class CustomFieldController extends StoreManagementController
{
	public function init()
	{
		$this->loadLanguageFile('backend/Category');
		$this->loadLanguageFile('backend/CustomField');
		return parent::init();
	}

	public function index()
	{
		$nodes = array();
		foreach (EavField::getEavClasses() as $class => $id)
		{
			$nodes[] = array('ID' => $id, 'name' => $this->translate($class));
		}

		// get offline payment methods
		$offlineMethods = array();
		foreach (OfflineTransactionHandler::getEnabledMethods() as $method)
		{
			$id = substr($method, -1);
			$offlineMethods[] = array('ID' => $method, 'name' => $this->config->get('OFFLINE_NAME_' . $id));
		}

		if ($offlineMethods)
		{
			$nodes[] = array('ID' => 'offline methods', 'name' => $this->translate('_offline_methods'), 'sub' => $offlineMethods);
		}

		return new ActionResponse('nodes', $nodes);
	}
}

?>
