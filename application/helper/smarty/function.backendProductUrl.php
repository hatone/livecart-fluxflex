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
 * Generates product form URL in backend
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_backendProductUrl($params, LiveCartSmarty $smarty)
{
	if (!isset($params['product']) && isset($params['id']))
	{
		$params['product'] = array('ID' => $params['id']);
	}

	$product = $params['product'];

	$urlParams = array('controller' => 'backend.category',
					   'action' => 'index' );

	return $smarty->getApplication()->getRouter()->createUrl($urlParams, true) . '#product_' . (!empty($product['Parent']['ID']) ? $product['Parent']['ID'] : $product['ID']);
}

?>
