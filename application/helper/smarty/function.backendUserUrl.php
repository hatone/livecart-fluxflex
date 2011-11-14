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
 * Generates user form URL in backend
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_backendUserUrl($params, LiveCartSmarty $smarty)
{
	if (!isset($params['user']) && isset($params['id']))
	{
		$params['user'] = array('ID' => $params['id']);
	}

	$urlParams = array('controller' => 'backend.userGroup',
					   'action' => 'index' );

	return $smarty->getApplication()->getRouter()->createUrl($urlParams, true) . '#user_' . (isset($params['user']) ? $params['user']['ID'] : '');
}

?>
