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
 * Generates static page URL
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_pageUrl($params, LiveCartSmarty $smarty)
{
	if (!class_exists('StaticPage', false))
	{
		ClassLoader::import('application.model.staticpage.StaticPage');
	}

	if (isset($params['id']))
	{
		$params['data'] = StaticPage::getInstanceById($params['id'], StaticPage::LOAD_DATA)->toArray();
	}

	$urlParams = array('controller' => 'staticPage',
					   'action' => 'view',
					   'handle' => $params['data']['handle'],
					   );

	return $smarty->getApplication()->getRouter()->createUrl($urlParams, true);
}

?>
