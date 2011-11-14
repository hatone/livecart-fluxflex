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

/**
 *  Allows to plug in controller response post-processors
 *
 *  @package application
 *  @author Integry Systems
 */
abstract class ControllerPlugin
{
	private $mustStop;

	protected $response;

	protected $controller;

	protected $request;

	protected $controllerName;

	protected $actionName;

	public abstract function process();

	public function __construct(Response $response, Controller $controller)
	{
		$this->response = $response;
		$this->controller = $controller;
		$this->request = $controller->getRequest();
		$this->application = $controller->getApplication();
		$this->config = $this->application->getConfig();
		$this->controllerName = $this->request->getControllerName();
		$this->actionName = $this->request->getActionName();
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function setResponse(Response $response)
	{
		$this->response = $response;
	}

	/**
	 *	Stop further execution of plugin chain for this action
	 */
	public function stop()
	{
		$this->mustStop = true;
	}

	public function isStopped()
	{
		return $this->mustStop;
	}
}

?>
