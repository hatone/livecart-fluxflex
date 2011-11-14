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

ClassLoader::import("framework.ApplicationException");

/**
 * Thrown when controller specified in request does not exists.
 *
 * @package	framework
 * @author Integry Systems
 */
class ControllerNotFoundException extends ApplicationException
{
	/**
	 * Name of controller that was not found
	 */
	private $controllerName;

	/**
	 * @param string $controller Controllers name
	 */
	public function __construct($controllerName)
	{
		parent::__construct("Specified controller ($controllerName) does not exist");
		$this->controllerName = $controllerName;
	}

	/**
	 * Gets a controller name
	 *
	 * @return string Controller name
	 */
	public function getControllerName()
	{
		return $this->controllerName;
	}
}

?>
