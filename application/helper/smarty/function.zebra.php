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
 * "Zebra" style table row formatting
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_zebra($params, LiveCartSmarty $smarty)
{
	static $internalCounter = 0;

	$loop = $params['loop'];

	if (!isset($smarty->_foreach[$loop]))
	{
		$loop = 'internal';
		$total = 0;
		$iteration = isset($smarty->zebra[$loop]) ? $smarty->zebra[$loop] + 1 : 1;
	}
	else
	{
		$total = $smarty->_foreach[$loop]['total'];
		$iteration = $smarty->_foreach[$loop]['iteration'];
	}

	if (empty($iteration) || !isset($smarty->zebra[$loop]))
	{
		$smarty->zebra[$loop] = 0;
		$iteration = 1;
	}

	$firstOrLast = '';
	if (1 == $iteration)
	{
		$firstOrLast = ' first';
	}
	if ($total == $iteration)
	{
		$firstOrLast = ' last';
	}

	if (!isset($params['stop']))
	{
		++$smarty->zebra[$loop];
	}

	return 'zebra ' . ($smarty->zebra[$loop] % 2 ? 'even' : 'odd') . $firstOrLast;
}

?>
