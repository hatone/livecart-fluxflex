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
 * Displays ActiveGrid table
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_activeGrid($params, LiveCartSmarty $smarty)
{
	if (!isset($params['rowCount']) || !$params['rowCount'])
	{
		$params['rowCount'] = 15;
	}

	foreach ($params as $key => $value)
	{
		$smarty->assign($key, $value);
	}

	if (isset($params['filters']) && is_array($params['filters']))
	{
		$smarty->assign('filters', $params['filters']);
	}

	$smarty->assign('url', $smarty->getApplication()->getRouter()->createUrl(array('controller' => $params['controller'], 'action' => $params['action']), true));

	$smarty->assign('thisMonth', date('m'));
	$smarty->assign('lastMonth', date('Y-m', strtotime(date('m') . '/15 -1 month')));

	return $smarty->display('block/activeGrid/gridTable.tpl');
}

?>
