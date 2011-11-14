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
 * Validator plugin base class
 *
 * @package application
 * @author Integry Systems
 */
abstract class ValidatorPlugin
{
	protected $validator;

	protected $application;

	public abstract function process();

	public function __construct(RequestValidator $validator, LiveCart $application)
	{
		$this->validator = $validator;
		$this->application = $application;
	}
}

?>
