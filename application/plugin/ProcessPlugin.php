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
 * Application run-time event plugin (startup, shutdown, etc.)
 *
 * @package application.plugin
 * @author Integry Systems
 */
abstract class ProcessPlugin
{
	protected $application;

	public function __construct($application)
	{
		$this->application = $application;
	}

	public function getApplication()
	{
		return $this->application;
	}

	abstract public function process();
}

?>
