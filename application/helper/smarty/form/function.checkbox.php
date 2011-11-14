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
function smarty_function_checkbox($params, $smarty)
{
	$formParams = $smarty->_tag_stack[0][1];
	$formHandler = $formParams['handle'];
	if (!($formHandler instanceof Form))
	{
		throw new HelperException('Element must be placed in {form} block');
	}
	$fieldName = $params['name'];

	if (!isset($params['id']))
	{
	  	$params['id'] = $params['name'];
	}

	if(!isset($params['value']))
	{
		$params['value'] = 1;
	}

	// Check permissions
	if($formParams['readonly'])
	{
		$params['disabled'] = 'disabled';
	}

	$formValue = $formHandler->get($fieldName);

	// get checkbox state if the form has been submitted
	if (1 == $formHandler->get('checkbox_' . $fieldName))
	{
		if ($formValue == $params['value'] || ('on' == $params['value'] && 1 == $formValue))
		{
			$params['checked'] = 'checked';
		}
		else
		{
			unset($params['checked']);
		}
	}
	else if ($params['value'] == $formValue)
	{
		$params['checked'] = 'checked';
	}

	if (empty($params['checked']))
	{
		unset($params['checked']);
	}

	$output = '<input type="checkbox"';
	foreach ($params as $name => $value)
	{
		$output .= ' ' . $name . '="' . $value . '"';
	}

	$output .= '/><input type="hidden" name="checkbox_' . $params['name'] . '" value="1" />';

	return $output;
}

?>
