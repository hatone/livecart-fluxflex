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
 * Exception that indicates an attempt to execute a restricted controller/action
 *
 * @package framework.controller.exception
 * @author Integry Systems
 */
class UnauthorizedException extends HTTPStatusException
{
	const STATUS_CODE = 401;

	public function __construct(Controller $controller, $message = false)
	{
		parent::__construct($controller, self::STATUS_CODE, $message);
	}
}

?>
