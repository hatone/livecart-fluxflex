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

ClassLoader::import("framework.request.validator.check.Check");

/**
 * E-mail address validator class
 *
 * @package framework.request.validator.check
 * @author Integry Systems
 */
class IsValidEmailCheck extends Check
{
	public function isValid($value)
	{
		return preg_match("/^[a-zA-Z0-9][a-zA-Z0-9\._-]+@[a-zA-Z0-9_-][a-zA-Z0-9\._-]+\.[a-zA-Z]{2,}$/" , $value);
	}
}

?>
