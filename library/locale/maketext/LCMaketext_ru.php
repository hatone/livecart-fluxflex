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

require_once('LocaleMaketext.php');

/**
 *
 * @package library.locale.maketext
 * @author Integry Systems 
 */
class LCMaketext_ru extends LocaleMaketext
{  
  	function quant($args)
	{
		$num   = $args[0];
		$forms = array_slice($args,1);

		$_return = "$num ";
	
		if ($num % 100 > 10 && $num % 100 < 20) 
		{
		  	$_return .= $forms[2];		  	
		} 
		else if ($num % 10 == 1) 
		{		  
		  	$_return .= $forms[0];	
		} 
		else if ($num % 10 == 0) 
		{		  
		  	$_return .= $forms[2];	
		} 
		else if ($num % 10 < 5)
		{		  
		  	$_return .= $forms[1];
		} 
		else 
		{		  
		  	$_return .= $forms[2];
		} 
 
		return $_return;
	}		
} 

?>
