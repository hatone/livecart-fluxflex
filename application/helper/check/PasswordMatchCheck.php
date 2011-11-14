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
 * Checks if entered passwords match
 *
 * @package application.helper.check
 * @author Integry Systems 
 */
class PasswordMatchCheck extends Check
{
	private $request;
	
	public function __construct($violationMsg, Request $request, $fieldName, $confFieldName)
	{
		parent::__construct($violationMsg);
		$this->setParam("fieldName", $fieldName);
		$this->setParam("confFieldName", $confFieldName);
		$this->request = $request;
	}
	
	public function isValid($value)
	{
		return $this->request->get($this->getParam("fieldName")) 
				== $this->request->get($this->getParam("confFieldName"));
	}
}

?>
