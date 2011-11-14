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
 *  Part of the LiveCart front controller that is only used when URL rewriting is not available
 *
 *  @author Integry Systems
 *  @package application
 */

define('NOREWRITE', false);

// Indicates that URL rewriting is disabled
$_GET['noRewrite'] = true;

// Apache: index.php/route
if (preg_match('/^Apache/', $_SERVER['SERVER_SOFTWARE']) && !NOREWRITE)
{
	$_GET['route'] = isset($_SERVER['PATH_INFO']) ? substr($_SERVER['PATH_INFO'], 1) : '';

	if (substr($_GET['route'], 0, 9) == 'index.php')
	{
		$_GET['route'] = substr($_GET['route'], 9);
	}

	if (substr($_GET['route'], 0, 1) == '/')
	{
		$_GET['route'] = substr($_GET['route'], 1);
	}

	$_SERVER['virtualBaseDir'] = $_SERVER['SCRIPT_NAME'] . '/';
}

// IIS, etc.: index.php?route=
else
{
	$_SERVER['virtualBaseDir'] = $_SERVER['SCRIPT_NAME'] . '?route=';
}

$_SERVER['baseDir'] = dirname($_SERVER['SCRIPT_NAME']) . '/public/';

include 'public/index.php';

?>
