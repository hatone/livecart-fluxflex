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
 * Abstract request value filter
 *
 * @package framework.request.validator.filter
 * @author Integry Systems
 */
abstract class RequestFilter
{
	private $paramList = array();
	
	protected function setParam($name, $value)
	{
		$this->paramList[$name] = $value;
	}

	public function getParam($name)
	{
		return $this->paramList[$name];
	}
	
	public function getParamList()
	{
		return $this->paramList;
	}

	abstract public function apply($value);	
}

?>
