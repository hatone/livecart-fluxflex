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
 * Generates order form URL in backend
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems 
 */
function smarty_function_backendOrderUrl($params, LiveCartSmarty $smarty)
{		
	$urlParams = array('controller' => 'backend.customerOrder', 
					   'action' => 'index' );
					   
	$url = $smarty->getApplication()->getRouter()->createUrl($urlParams, true) . '#order_' . (isset($params['order']) ? $params['order']['ID'] . '#tabOrderInfo__' : '');
	
	if (isset($params['url']))
	{
		$url = $smarty->getApplication()->getRouter()->createFullUrl($url);
	}
	
	return $url;
}

?>
