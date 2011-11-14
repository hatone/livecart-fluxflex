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
 * Strip non-numeric characters from a string
 *
 * @package framework.request.validator.filter
 * @author Integry Systems
 */
class NumericFilter extends RequestFilter
{
	public function apply($value)
	{
		$value = str_replace(',' , '.', $value);

		$parts = explode('.', $value);

		// next remove all characters save 0 though 9
		// in both elements of the array
		
		// todo: eregi_replace is deprecated in 5.3
		$dollars = @eregi_replace("[^-0-9]", null, $parts[0]);
		if (isset($parts[1]))
		{
			$cents = @eregi_replace("[^0-9]", null, $parts[1]);
		}
		else
		{
		  	$cents = null;
		}

		// if there was a decimal in the original string, put it back
		if((string)$cents != null)
		{
		   $cents = "." . $cents;
		}

		return $dollars . $cents;
	}
}

?>
