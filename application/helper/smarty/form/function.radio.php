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
 * Display radio button
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 * 
 * @package application.helper.smarty.form
 * @author Integry Systems <http://integry.com>
 */
function smarty_function_radio($params, $smarty) 
{
	$formParams = $smarty->_tag_stack[0][1];
	$formHandler = $formParams['handle'];
	if (!($formHandler instanceof Form))
	{
		throw new HelperException('Element must be placed in {form} block');
	}
	$fieldName = $params['name'];
		
	
	// Check permissions
	if($formParams['readonly'])
	{
		$params['disabled'] = 'disabled';
	}
	
	// get checked state
	$formValue = $formHandler->get($fieldName);
	if ($formValue == $params['value'] || (empty($formValue) && $params['checked']))
	{
		$params['checked'] = 'checked';
	}

	$output = '<input type="radio"';
	foreach ($params as $name => $value)
	{
		$output .= ' ' . $name . '="' . $value . '"';
	}

	$output .= "/>";
		
	return $output;
}

?>
