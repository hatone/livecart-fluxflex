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

ClassLoader::import("framework.response.CompositeResponse");

/**
 * Composite JSON response - allows to generate multiple page fragments within one request
 *
 * Useful with AJAX calls for which a single request may affect several parts of the user interface
 *
 * @package framework.response
 * @author	Integry Systems
 */
class CompositeJSONResponse extends CompositeResponse
{
	protected $data = array();

	public function __construct()
	{
		$this->setHeader('Cache-Control', 'no-cache, must-revalidate');
		$this->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$this->setHeader('Content-type', 'text/javascript');
	}

	public function getData()
	{
		return json_encode($this->data);
	}

	public function setResponse($outputHandle, Response $response)
	{
		if ($response instanceof JSONResponse)
		{
			$this->set($outputHandle, $response->getValue());
		}
		else
		{
			parent::setResponse($outputHandle, $response);
		}
	}
}

?>
