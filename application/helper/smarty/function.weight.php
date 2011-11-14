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
 * ...
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_weight($params, LiveCartSmarty $smarty)
{
	if(!isset($params['value']))
	{
		throw new ApplicationException("Please use 'value' attribute to specify weight");
	}
	
	$application = $smarty->getApplication();
	
	$units_hi = $application->translate($application->getConfig()->get('UNIT_SYSTEM') == 'ENGLISH' ? '_units_pounds' : '_units_kg');
	$units_lo = $application->translate($application->getConfig()->get('UNIT_SYSTEM') == 'ENGLISH' ? '_units_ounces' : '_units_g');

	$value_hi = (int)$params['value'];
	$value_lo = str_replace('0.', '', (string)($params['value'] - (int)$params['value']));
	
	return sprintf("%s %s %s %s", $value_hi, $units_hi, $value_lo, $units_lo);

}

?>
