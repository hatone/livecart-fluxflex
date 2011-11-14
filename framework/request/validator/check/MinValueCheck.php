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
 * 
 * @package framework.request.validator.check
 * @author Integry Systems 
 */
class MinValueCheck extends Check
{
	public function __construct($violationMsg, $minValue)
	{
		parent::__construct($violationMsg);
		$this->setParam("minValue", $minValue);
	}
	
	public function isValid($value)
	{
		if ($value >= $this->getParam("minValue"))
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
