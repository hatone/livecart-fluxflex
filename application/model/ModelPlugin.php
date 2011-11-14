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
 * Model plugin base class
 *
 * @package application.model
 * @author Integry Systems
 */
abstract class ModelPlugin
{
	protected $object;

	protected $application;
	
	public abstract function process();
	
	public function __construct(&$object, LiveCart $application)
	{
		$this->object =& $object;	
		$this->application = $application;
		$this->process();
	}
}

?>
