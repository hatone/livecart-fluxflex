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

ClassLoader::import("application.controller.backend.abstract.BackendController");

/**
 * Backend error pages
 *
 * @package application.controller.backend
 * @author Integry Systems
 */
class ErrController extends BackendController
{
	public function index()
	{
		$response = new ActionResponse();
		$response->set('id', $this->request->get('id'));
		$response->set('ajax', $this->request->get('ajax'));
		$response->set('description', HTTPStatusException::getCodeMeaning($this->request->get('id')));

		return $response;
	}

	public function redirect()
	{
		$id = $this->request->get('id');
		$params = array();

		if($this->isAjax())
		{
			$params['query'] = array('ajax' => 1);
		}

		switch($id)
		{
			case 401:
				$response = new ActionRedirectResponse('backend.session', 'index', $params);
				if ($this->isAjax())
				{
					return new JSONResponse(array('__redirect' => $response->getUrl($this->router)));
				}
				else
				{
					return $response;
				}
			case 403:
			case 404:
				$params['id'] = $id;
				return new ActionRedirectResponse('backend.err', 'index', $params);
			default:
			   	return new RawResponse('error ' . $this->request->get('id'));
		}
	}
}

?>
