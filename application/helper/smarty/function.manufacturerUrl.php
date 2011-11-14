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

include_once dirname(__file__) . '/function.categoryUrl.php';

/**
 * Generates manufacturer page URL
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_manufacturerUrl($params, LiveCartSmarty $smarty)
{
	$manufacturer = $params['data'];
	$params['data'] =& Category::getRootNode()->toArray();
	$params['addFilter'] = new ManufacturerFilter($manufacturer['ID'], $manufacturer['name']);
	return createCategoryUrl($params, $smarty->getApplication());
}


?>
