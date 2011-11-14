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
 *  Initialize framework, load main classes
 *  @package application
 *  @author Integry Systems
 */

if (isset($_REQUEST['stat']))
{
	function __autoload($className)
	{
		static $stat;
		if (!$stat)
		{
			$stat = $GLOBALS['stat'];
		}

		$start = microtime(true);
		ClassLoader::load($className);
		$elapsed = microtime(true) - $start;

		if (empty($GLOBALS['ClassLoaderTime']))
		{
			$GLOBALS['ClassLoaderTime'] = $GLOBALS['ClassLoaderCount'] = 0;
		}

		$GLOBALS['ClassLoaderTime'] += $elapsed;
		$GLOBALS['ClassLoaderCount']++;
	}
}

require_once(dirname(dirname(__file__)) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'ClassLoader.php');

ClassLoader::mountPath('.', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

if (defined('CACHE_DIR'))
{
	ClassLoader::mountPath('cache', CACHE_DIR);
}

$classLoaderCacheFile = ClassLoader::getRealPath('cache.') . 'classloader.php';
if (file_exists($classLoaderCacheFile))
{
	$classLoaderCache = include $classLoaderCacheFile;
	ClassLoader::setRealPathCache($classLoaderCache['realPath']);
	ClassLoader::setMountPointCache($classLoaderCache['mountPoint']);
}

if (isset($_REQUEST['stat']))
{
	ClassLoader::import('library.stat.Stat');
	$stat = new Stat(true);
	$GLOBALS['stat'] = $stat;
}

ClassLoader::import('framework.request.Request');
ClassLoader::import('framework.request.Router');
ClassLoader::import('framework.controller.*');
ClassLoader::import('framework.response.*');
ClassLoader::import('application.controller.*');
ClassLoader::import('application.model.*');
ClassLoader::import('application.model.system.*');
ClassLoader::import('application.LiveCart');

?>
