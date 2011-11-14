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
 *  Decorates Check object
 *
 *  @package framework.request.validator.check
 *  @author Integry Systems
 */
class ConditionalCheck extends Check
{
	protected $check;
	protected $condition;
		
	function __construct(CheckCondition $condition, Check $check)
	{
		$this->check = $check;	
		$this->condition = $condition;	
	}	
	
	public function getViolationMsg()
	{
		return $this->check->getViolationMsg();
	}
	
	protected function setParam($name, $value)
	{
		$this->check->setParam($name, $value);
	}
	
	public function getParam($name)
	{
		return $this->check->getParam($name);
	}
	
	public function getParamList()
	{
		return $this->check->getParamList();
	}
	
	public function isValid($value)
	{
		if ($this->condition->isSatisfied())
		{
			return $this->check->isValid($value);		 
		}
		else
		{
			// skip validation if the condition is not satisfied
			return true;
		}
	}	
}

?>
