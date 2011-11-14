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
 * Apply any Checks to arbitrary runtime variable values (rather than request values)
 *
 * <code>
 * 	$validator->addCheck('formFieldOrSomeIdentifier', new VariableCheck($_COOKIE['myCookie'], new IsNotEmptyCheck("Sorry, cookie not set!")));
  * </code>
 *
 * @package framework.request.validator.check
 * @author Integry Systems
 */
class VariableCheck extends Check
{
	protected $check;
	protected $value;

	public function __construct($value, Check $check)
	{
		parent::__construct($check->getViolationMsg());
		$this->check = $check;
		$this->value = $value;
	}

	public function isValid($value)
	{
		return $this->check->isValid($this->value);
	}
}

?>
