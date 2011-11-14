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
 * Creates a MakeText translation handler instance for required locale
 *
 * @return LocaleMaketext
 * @author Integry Systems
 * @package library.locale.maketext
 */
class LCMakeTextFactory
{
	/**
	 * Creates a MakeText translation handler instance for required locale
	 * @return LCMakeText
	 */	
  	function create($locale)
	{	
	  	$classname = 'LCMakeText_' . strtolower($locale);
		$classfile = dirname(__FILE__) . '/' . $classname . '.php';

	  	if (file_exists($classfile)) 
		{
		 	require_once($classfile);
			$instance = new $classname;
		}
		else
		{
		  	require_once('LocaleMaketext.php');
		  	$instance = new LocaleMaketext;
		}
		
		return $instance;
	}
} 
?>
