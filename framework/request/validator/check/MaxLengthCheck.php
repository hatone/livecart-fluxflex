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
 * Check for max string length validation (fails if string is longer than $maxLength)
 *
 * @package framework.request.validator.check
 * @author Integry Systems
 */
class MaxLengthCheck extends Check
{
	public function __construct($violationMsg, $maxLength)
	{
		parent::__construct($violationMsg);
		$this->setParam("maxLength", $maxLength);
	}
	
	public function isValid($value)
	{
		if (strlen($value) <= $this->getParam("maxLength"))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
}

?>
