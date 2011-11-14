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
 *
 * @package library.json
 * @author Integry Systems
 */

if (!function_exists('json_encode'))
{
	function json_encode($value)
	{
		if (!class_exists('Services_JSON', false))
		{
		  	include 'Services_JSON.php';
		}

		$inst = new Services_JSON();
		return $inst->encode($value);
	}

	function json_decode($value)
	{
		if (!class_exists('Services_JSON', false))
		{
		  	include 'Services_JSON.php';
		}

		$inst = new Services_JSON();
		return $inst->decode($value);
	}
}

?>
