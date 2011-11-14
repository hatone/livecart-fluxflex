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
 * Strip HTML tags from a string
 *
 * @package framework.request.validator.filter
 * @author Integry Systems
 */
class StripHtmlFilter extends RequestFilter
{
	public function apply($value)
	{
		return strip_tags($value);
	}
}

?>
