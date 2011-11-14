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
 *  Return an alternative value if the primary value is empty.
 *
 *  This is useful for short-hand syntax in templates where it is necessary
 *	to display a value that may possibly be stored in different variables.
 *
 *  For example:
 *  {$product.shortDescription|@or:$product.longDescription}
 *
 *  @package application.helper.smarty
 *  @author Integry Systems
 */
function smarty_modifier_or($string, $alternative)
{
	if (empty($string))
	{
		return $alternative;
	}
	else
	{
		return $string;
	}
}

?>
