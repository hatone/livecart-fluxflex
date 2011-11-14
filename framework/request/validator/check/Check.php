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

ClassLoader::import("framework.request.validator.check.CheckException");

/**
 * Variable validation logic container
 *
 * @package framework.request.validator.check
 * @author Integry Systems
 */
abstract class Check
{
	protected $violationMsg = "";
	protected $paramList = array();

	public function __construct($violationMsg)
	{
		$this->violationMsg = $violationMsg;
	}

	public function getViolationMsg()
	{
		return $this->violationMsg;
	}

	protected function setParam($name, $value)
	{
		$this->paramList[$name] = $value;
	}

	public function getParam($name)
	{
		if (isset($this->paramList[$name]))
		{
			return $this->paramList[$name];
		}
	}

	public function getParamList()
	{
		return $this->paramList;
	}

	abstract public function isValid($value);
}

?>
