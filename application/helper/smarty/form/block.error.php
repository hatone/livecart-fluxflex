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
 * Form field error message block
 *
 * @package application.helper.smarty.form
 * @author Integry Systems
 */
function smarty_block_error($params, $content, $smarty, &$repeat) {
	$smarty->assign("msg", "");

	$formParams = $smarty->_tag_stack[0][1];
	$formHandler = $formParams['handle'];

	if (!$formHandler)
	{
		return $content;
	}

	$validator = $formHandler->getValidator();
	$errorMsg = "";
	if (empty($params))
	{
		$errorList = $validator->getErrorList();
		if (empty($errorList))
		{
			$content = "";
		}
	}

	if (!empty($params['list']))
	{
		$smarty->assign($params['list'], $validator->getErrorList);
	}

	if (!empty($params['for']))
	{
		$msgVarName = "msg";
		$fieldName = $params['for'];
		$errorList = $validator->getErrorList();
		$errorMsg = "";

		if (!empty($params['msg']))
		{
			$msgVarName = $params['msg'];
		}
		if (!empty($errorList[$fieldName]))
		{
			$errorMsg = $errorList[$fieldName];
			$smarty->assign($msgVarName, $errorMsg);
		}
		else
		{
			$content = "";
			$smarty->assign($msgVarName, "");
		}
	}

	return $content;
}

?>
