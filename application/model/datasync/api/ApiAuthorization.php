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
 * Abstract class for API authorization methods
 *
 * @package application.model.datasync
 * @author Integry Systems <http://integry.com>
 *
 */

abstract class ApiAuthorization
{
	protected $application;

	public function __construct(LiveCart $application, $params)
	{
		$this->application = $application;
		$this->params = $params;
	}

	abstract public function isValid();

	public function isAuthorized()
	{
		// leave room for overriding authorization result for special cases
		return $this->isValid();
	}
}

?>
