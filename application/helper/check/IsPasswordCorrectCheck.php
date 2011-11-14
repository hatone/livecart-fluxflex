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
 * @package application.helper.check
 * @author Integry Systems
 */
class IsPasswordCorrectCheck extends Check
{
	private $user;

	public function __construct($violationMsg, User $user)
	{
		parent::__construct($violationMsg);
		$this->user = $user;
	}

	public function isValid($value)
	{
		return $this->user->isPasswordValid($value);
	}
}

?>
