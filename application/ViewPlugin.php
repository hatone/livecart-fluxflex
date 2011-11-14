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
 * View plugin base class
 *
 * @package application.model
 * @author Integry Systems
 */
abstract class ViewPlugin
{
	protected $smarty;

	protected $application;
	
	public abstract function process($code);
	
	public function __construct(LiveCartSmarty $smarty, LiveCart $application)
	{
		$this->smarty = $smarty;
		$this->application = $application;
	}
}

?>
