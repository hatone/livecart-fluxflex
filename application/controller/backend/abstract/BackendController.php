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

ClassLoader::import("application.controller.BaseController");
ClassLoader::import("library.json.json");
ClassLoader::import('application.controller.backend.BackendToolbarController');
ClassLoader::import('application.controller.backend.ModuleController');

/**
 * Generic backend controller for administrative tools (actions, modules etc.)
 *
 * @author Integry Systems
 * @package application.backend.controller.abstract
 */
abstract class BackendController extends BaseController
{
	public function __construct(LiveCart $application)
	{
		if ($application->getConfig()->get('SSL_BACKEND'))
		{
			$application->getRouter()->setSslAction('');
		}

		parent::__construct($application);

		if (!isset($_SERVER['HTTP_USER_AGENT']))
		{
			$_SERVER['HTTP_USER_AGENT'] = 'Firefox';
		}

		// no IE yet
		if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT']))
		{
			ClassLoader::import('application.controller.backend.UnsupportedBrowserException');
			throw new UnsupportedBrowserException();
		}

		if (!$this->user->hasBackendAccess() && !($this instanceof SessionController))
		{
			SessionUser::destroy();

			$url = $this->router->createUrl(array('controller' => 'backend.session', 'action' => 'index'));
			if (!$this->isAjax())
			{
				header('Location: ' . $url);
			}
			else
			{
				header('Content-type: text/javascript');
				echo json_encode(array('__redirect' => $url));
			}

			exit;
		}
	}

	public function init()
	{
	  	$this->setLayout('empty');
		$this->addBlock('USER_MENU', 'boxUserMenu', 'block/backend/userMenu');
		$this->addBlock('TRANSLATIONS', 'translations', 'block/backend/translations');

		$this->addBlock('FOOTER_TOOLBAR', 'toolbar', 'block/backend/toolbar');

		$this->getPendingModuleUpdateStats($this->application);

		return parent::init();
	}

	protected function getPendingModuleUpdateStats(LiveCart $application)
	{
		$config = $application->getConfig();

		// modules needing update
		if (!$config->isValueSet('MODULE_STATS_UPDATED') || (time() - $config->get('MODULE_STATS_UPDATED') > 3600))
		{
			$config->set('MODULE_STATS_UPDATED', time());
			$config->save();
			$controller = new ModuleController($this->application);
			$controller->initRepos();
			$updateResponse = $controller->index();
			$modules = $updateResponse->get('sortedModules');
			$config->set('MODULE_STATS_NEED_UPDATING', isset($modules['needUpdate']) ? count($modules['needUpdate']) : 0);
			$config->save();

			foreach ($this->getConfigFiles() as $file)
			{
				$this->loadLanguageFile($file);
			}
		}
	}

	public function boxUserMenuBlock()
	{
		return $this->translationsBlock();
	}

	public function translationsBlock()
	{
		return new BlockResponse('languageData', json_encode($this->locale->translationManager()->getLoadedDefinitions()));
	}

	public function toolbarBlock()
	{
		$response = new BlockResponse();
		$response->set('dropButtons',
			BackendToolbarItem::sanitizeItemArray(
				BackendToolbarItem::getUserToolbarItems(BackendToolbarItem::TYPE_MENU)
			)
		);
		return $response;
	}
}

?>
