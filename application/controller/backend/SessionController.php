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
ClassLoader::import('application.model.user.SessionUser');

/**
 * Product Category controller
 *
 * @package application.controller.backend
 * @author Integry Systems
 *
 */
class SessionController extends StoreManagementController
{
	public function index()
	{
		$this->loadLanguageFile('User');
		$response = new ActionResponse('email', $this->request->get('email'));
		$response->setHeader('NeedLogin', 1);
		return $response;
	}

	/**
	 *  Process actual login
	 */
	public function doLogin()
	{
		$user = User::getInstanceByLogin($this->request->get('email'), $this->request->get('password'));
		if (!$user)
		{
			return new ActionRedirectResponse('backend.session', 'index', array('query' => array('failed' => 'true', 'email' => $this->request->get('email'))));
		}

		// login
		SessionUser::setUser($user);

		return new ActionRedirectResponse('backend.index', 'index');
	}

	public function logout()
	{
		SessionUser::destroy();
		return new ActionRedirectResponse('backend.session', 'index');
	}
}

?>
