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
 * Displays backend language selection menu
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_backendLangMenu($params, LiveCartSmarty $smarty)
{
  	if (!$smarty->getApplication()->getLanguageArray())
  	{
		return false;
	}

	$smarty->assign('currentLang', Language::getInstanceByID($smarty->getApplication()->getLocaleCode())->toArray());
	$smarty->assign('returnRoute', base64_encode($smarty->getApplication()->getRouter()->getRequestedRoute()));
	return $smarty->display('block/backend/langMenu.tpl');
}

?>
