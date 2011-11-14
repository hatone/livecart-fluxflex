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

ClassLoader::import("application.controller.FrontendController");
ClassLoader::import('application.model.staticpage.StaticPage');

/**
 * Displays static pages
 *
 * @author Integry Systems
 * @package application.controller
 */
class StaticPageController extends FrontendController
{
	public function foo()
	{
		return new RawResponse('Hello');
	}

	public function view()
	{
		$page = StaticPage::getInstanceByHandle($this->request->get('handle'));

		if ($parent = $page->parent->get())
		{
			while ($parent)
			{
				$parent->load();
				$urlParams = array('controller' => 'staticPage',
								   'action' => 'view',
								   'handle' => $parent->handle->get(),
								   );

				$this->addBreadCrumb($parent->getValueByLang('title'), $this->router->createUrl($urlParams, true));
				$parent = $parent->parent->get();
			}
		}

		$pageArray = $page->toArray();
		$this->addBreadCrumb($pageArray['title_lang'], '');

		$response = new ActionResponse('page', $pageArray);
		$response->set('subPages', $page->getSubPageArray());
		return $response;
	}
}

?>
