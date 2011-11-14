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
 *  Abstract class for defining condition under which a particular validation have to be performed
 *
 *  This is useful for creating more complex forms where validation rules on the same field may
 *  differ depending on a value that is entered in another field.
 *
 *  Conditional checks are only available at server side (for now)
 *
 *  @package framework.request.validator.check
 *  @author Integry Systems
 */
abstract class CheckCondition
{
	protected $request;
	
	function __construct(Request $request)
	{
		$this->request = $request;			  
	}
	
	abstract function isSatisfied();
}

?>
