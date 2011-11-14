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
 * @package application.helper.smarty.form
 * @author Integry Systems
 */
function smarty_function_password($params, $smarty)
{
	$formParams = $smarty->_tag_stack[0][1];
	$formHandler = $formParams['handle'];
	if (!($formHandler instanceof Form))
	{
		throw new HelperException('Element must be placed in {form} block');
	}
	$fieldName = $params['name'];

	$output = '<input type="password"';
	
	// Check permissions
	if($formParams['readonly'])
	{	
		$params['disabled'] = 'disabled';
	}
	
	if (isset($params['value']))
	{
		unset($params['value']);
	}
	
	foreach ($params as $name => $value)
	{
		$output .= ' ' . $name . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
	}

	$output .= " />";

	return $output;
}

?>
