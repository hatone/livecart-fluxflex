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

ClassLoader::import('framework.response.Response');

/**
 * Process another action without a physical redirect to another URL
 *
 * This is usually useful for various kinds of error pages for which URL redirection is not desirable
 *
 * @see Application
 * @package	framework.response
 * @author	Integry Systems
 */
class InternalRedirectResponse extends Response
{
	/**
	 * Controllers name to redirect to
	 */
	private $controllerName = "";

	/**
	 * Action name to redirect to
	 */
	private $actionName = "";

	/**
	 * Array of parameters
	 */
	private $paramList = array();

	/**
	 * @param string $controllerName Controller name (for example, "product" for ProductController)
	 * @param string $actionName	Controller action name
	 * @param array $paramList additional parameters (for example, array('id' => 4, 'query' => 'more=params&other=true'))
	 */
	public function __construct($controllerName, $actionName, $paramList = array())
	{
		$this->setControllerName($controllerName);
		$this->setActionName($actionName);
		$this->setParamList($paramList);
	}

	/**
	 * Sets controller to redirect to
	 *
	 * @param string $controller Controller
	 * @return void
	 */
	public function setControllerName($controller)
	{
		$this->controllerName = $controller;
	}

	/**
	 * Gets controller to redirect to
	 *
	 * @return mixed null if no controller set, string otherwise
	 */
	public function getControllerName()
	{
		return $this->controllerName;
	}

	/**
	 * Sets action to redirect to
	 *
	 * @param string $action Action
	 * @return void
	 */
	public function setActionName($action)
	{
		$this->actionName = $action;
	}

	/**
	 * Gets action to redirect to
	 *
	 * @return mixed null if no action set, string otherwise
	 */
	public function getActionName()
	{
		return $this->actionName;
	}

	/**
	 * Sets parameters
	 *
	 * @param array $parameter Associative array with parameters
	 * @return void
	 */
	public function setParamList($paramList)
	{
		$this->paramList = $paramList;
	}

	/**
	 * Gets parameters
	 *
	 * @return array Associative array with paramaters
	 */
	public function getParamList()
	{
		return (array)$this->paramList;
	}


	public function getData()
	{
		return ;
	}
}

?>
