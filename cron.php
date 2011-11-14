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
 *  Front controller for LiveCart scheduled tasks
 *
 *  @author Integry Systems
 */

if (isset($_SERVER['REQUEST_URI']))
{
	$_SERVER['REQUEST_URI'] = dirname($_SERVER['REQUEST_URI']) . '/';
}
else
{
	// retrieve base URL from configuration
	$url = include dirname(__file__) . '/storage/configuration/url.php';
	$parsed = parse_url($url['url']);
	$_SERVER['HTTP_HOST'] = $parsed['host'];
	$_SERVER['REQUEST_URI'] = $parsed['path'];
	$_SERVER['REWRITE'] = $url['rewrite'];
}

if (isset($_SERVER['REWRITE']) && !$_SERVER['REWRITE'])
{
	$this->request->set('noRewrite', true);
//	$app->getRouter()->enableURLRewrite($_SERVER['rewrite']);
}

include dirname(__file__) . '/application/Initialize.php';
$app = new LiveCart();

if (isset($_SERVER['REWRITE']))
{
//	$app->getRouter()->enableURLRewrite($_SERVER['rewrite']);
}
$app->getCron()->process();

?>
