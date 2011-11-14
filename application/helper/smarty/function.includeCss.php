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

ClassLoader::import('application.model.system.CssFile');

/**
 * ...
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_includeCss($params, LiveCartSmarty $smarty)
{
	$fileName = $params['file'];
	$filePath = substr($fileName, 0, 1) != '/' ?
					ClassLoader::getRealPath('public.stylesheet.') .  $fileName :
					ClassLoader::getRealPath('public') .  $fileName;

	// fix slashes
	$filePath = str_replace('\\', DIRECTORY_SEPARATOR, $filePath);
	$filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

	if((!is_file($filePath) && !isset($params['external']))  || (substr($filePath, -4) != '.css'))
	{
		return;
	}

	$css = CssFile::getInstanceFromPath($filePath, $smarty->getApplication()->getTheme());

	$origFileName = $fileName;

	if ($css->isPatched())
	{
		$filePath = $css->getPatchedFilePath();
		$fileName = $css->getPatchedFileRelativePath();
	}

	if(isset($params['inline']) && $params['inline'] == 'true')
	{
		$path = 'stylesheet/' . str_replace(DIRECTORY_SEPARATOR, '/', $fileName) . '?' . filemtime($filePath);
		return '<link href="' . $path . '" media="screen" rel="Stylesheet" type="text/css" />' . "\n";
	}
	else if (isset($params['external']))
	{
		$smarty->_smarty_vars['INCLUDED_STYLESHEET_FILES_EXTERNAL'][] = $fileName;
	}
	else
	{
		$includedStylesheetTimestamp = $smarty->_smarty_vars['INCLUDED_STYLESHEET_TIMESTAMP'];
		if(!($includedStylesheetFiles = $smarty->_smarty_vars['INCLUDED_STYLESHEET_FILES']))
		{
		   $includedStylesheetFiles = array();
		}

		if(in_array($filePath, $includedStylesheetFiles))
		{
			if (isset($params['front']))
			{
				unset($includedStylesheetFiles[array_search($filePath, $includedStylesheetFiles)]);
			}
			else
			{
				return;
			}
		}

		$fileMTime = filemtime($filePath);
		if($fileMTime > (int)$includedStylesheetTimestamp)
		{
			$smarty->_smarty_vars['INCLUDED_STYLESHEET_TIMESTAMP'] = $fileMTime;
		}

		if (isset($params['front']))
		{
			array_unshift($includedStylesheetFiles, $filePath);
		}
		else if (isset($params['last']))
		{
			$includedStylesheetFiles['x' . ((count($includedStylesheetFiles) + 200) * (int)$params['last'])] = $filePath;
		}
		else
		{
			array_push($includedStylesheetFiles, $filePath);
		}

		$smarty->_smarty_vars['INCLUDED_STYLESHEET_FILES'] = $includedStylesheetFiles;
	}

	foreach ($smarty->getApplication()->getConfigContainer()->getFilesByRelativePath('public/stylesheet/' . $origFileName, true) as $file)
	{
		if (realpath($file) == realpath($filePath))
		{
			continue;
		}

		$file = substr($file, strlen(ClassLoader::getRealPath('public')));
		$params['file'] = $file;
		smarty_function_includeCss($params, $smarty);
	}
}
?>
