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
 * Generates static page title
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_pageName($params, LiveCartSmarty $smarty)
{
	if (!class_exists('StaticPage', false))
	{
		ClassLoader::import('application.model.staticpage.StaticPage');
	}

	if (!isset($params['id']))
	{
		return '<span style="color: red; font-weight: bold; font-size: larger;">No static page ID provided</span>';
	}

	$page = StaticPage::getInstanceById($params['id'], StaticPage::LOAD_DATA)->toArray();

	return $page[!empty($params['text']) ? 'text_lang' : 'title_lang'];
}

?>
