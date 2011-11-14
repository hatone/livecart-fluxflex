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
function smarty_function_filefield($params, $smarty)
{
	$formParams = $smarty->_tag_stack[0][1];
	$formHandler = $formParams['handle'];
	$fieldName = $params['name'];

	// Check permissions
	if($formParams['readonly'])
	{
		$params['disabled'] = 'disabled';
	}

	$formHandler->setParam('enctype', 'multipart/form-data');

	$content = '<input type="file"';
	foreach ($params as $name => $param)
	{
		$content .= ' ' . $name . '="' . $param . '"';
	}

	$content .= '/>';

	return $content;
}

?>
