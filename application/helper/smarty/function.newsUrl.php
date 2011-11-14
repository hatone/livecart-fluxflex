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

ClassLoader::importNow('application.helper.CreateHandleString');

/**
 * Generates news page URL
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_newsUrl($params, LiveCartSmarty $smarty)
{
	return createNewsPostUrl($params, $smarty->getApplication());
}

function createNewsPostUrl($params, LiveCart $application)
{
	$news = $params['news'];
	$urlParams = array('controller' => 'news',
					   'action' => 'view',
					   'handle' => createHandleString($news['title_lang']),
					   'id' => $news['ID']
					   );
	$router = $application->getRouter();
	$url = $router->createUrl($urlParams, true);
	if(array_key_exists('full', $params) && $params['full'] == true)
	{
		$url = $router->createFullUrl($url);
	}
	return $url;
}

?>
