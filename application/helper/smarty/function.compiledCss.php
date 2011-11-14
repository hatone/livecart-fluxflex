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
ClassLoader::import('application.model.template.Theme');

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
function smarty_function_compiledCss($params, LiveCartSmarty $smarty)
{
	$app = $smarty->getApplication();

	if (!$app->isBackend())
	{
		if (!function_exists('smarty_function_includeCss'))
		{
			include_once('function.includeCss.php');
		}

		$last = 1000;
		$files = array('common.css');
		$theme = new Theme($smarty->getApplication()->getTheme(), $app);
		foreach ($theme->getAllParentThemes() as $parentTheme)
		{
			$files[] = CssFile::getTheme($parentTheme) . '.css';
		}

		$files[] = CssFile::getTheme($smarty->getApplication()->getTheme()) . '.css';

		foreach ($files as $file)
		{
			smarty_function_includeCss(array('file' => '/upload/css/' . $file, 'last' => ++$last), $smarty);
		}
	}

	$includedStylesheetTimestamp = $smarty->_smarty_vars["INCLUDED_STYLESHEET_TIMESTAMP"];
	$includedStylesheetFiles = $smarty->_smarty_vars["INCLUDED_STYLESHEET_FILES"];

	if ($includedStylesheetFiles)
	{
		uksort($includedStylesheetFiles, 'strnatcasecmp');
	}

	$out = '';

	if(isset($params['glue']) && ($params['glue'] == true) && !$smarty->getApplication()->isDevMode() && (!$smarty->getApplication()->isCustomizationMode() || $app->isBackend()))
	{
		$request = $smarty->getApplication()->getRequest();

		if (isset($params['nameMethod']) && 'hash' == $params['nameMethod'])
		{
			$names = array_values($includedStylesheetFiles);
			sort($names);
			$compiledFileName = md5(implode("\n", $names)) . '.css';
		}
		else
		{
			$compiledFileName = $request->getControllerName() . '-' . $request->getActionName() . '.css';
		}

		$compiledFilePath = ClassLoader::getRealPath('public.cache.stylesheet.') .  $compiledFileName;
		$baseDir = ClassLoader::getRealPath('public.stylesheet.');
		$publicDir = ClassLoader::getRealPath('public.');

		$compiledFileTimestamp = 0;
		if(!is_file($compiledFilePath) || filemtime($compiledFilePath) < $includedStylesheetTimestamp)
		{
			if(!is_dir(ClassLoader::getRealPath('public.cache.stylesheet'))) mkdir(ClassLoader::getRealPath('public.cache.stylesheet'), 0777, true);

			// compile
			$compiledFileContent = "";
			foreach($includedStylesheetFiles as $key => $cssFile)
			{
				$relPath = str_replace($publicDir, '', $cssFile);
				$relPath = str_replace('\\', '/', $relPath);

				$compiledFileContent .= "\n\n\n/***************************************************\n";
				$compiledFileContent .= " * " . $relPath . "\n";
				$compiledFileContent .= " ***************************************************/\n\n";

				$content = file_get_contents($cssFile);
				$content = str_replace('url(..', 'url(' . dirname($relPath) . '/..', $content);
				$content = str_replace('url(\'..', 'url(\'' . dirname($relPath) . '/..', $content);
				$content = str_replace('url("..', 'url("' . dirname($relPath) . '/..', $content);

				$compiledFileContent .= $content;
			}

			$compiledFileContent = preg_replace('/\.(jpg|png|gif|bmp)/', '.$1?' . time(), $compiledFileContent);
			$compiledFileContent = preg_replace('/-moz-border-radius\:([ \.a-zA-Z0-9]+);/', '-moz-border-radius: $1; -khtml-border-radius: $1; border-radius: $1; ', $compiledFileContent);

			file_put_contents($compiledFilePath, $compiledFileContent);

			if (function_exists('gzencode'))
			{
				file_put_contents($compiledFilePath . '.gz', gzencode($compiledFileContent, 9));
			}
		}
		$compiledFileTimestamp = filemtime($compiledFilePath);

		$out = '<link href="gzip.php?file=' . $compiledFileName . '&amp;time=' . $compiledFileTimestamp . '" rel="Stylesheet" type="text/css"/>';
	}
	else if ($includedStylesheetFiles)
	{
		$includeString = "";
		$publicPath = ClassLoader::getRealPath('public.');

		foreach($includedStylesheetFiles as $cssFile)
		{
			$urlPath = str_replace('\\', '/', str_replace($publicPath, '', $cssFile));
			$includeString .= '<link href="' . $urlPath . '?' . filemtime($cssFile) . '" rel="Stylesheet" type="text/css"/>' . "\n";
		}

		$out = $includeString;
	}

	if ($externalFiles = $smarty->_smarty_vars["INCLUDED_STYLESHEET_FILES_EXTERNAL"])
	{
		foreach($externalFiles as $cssFile)
		{
			$out .= '<link href="' . $cssFile . '" rel="Stylesheet" type="text/css"/>' . "\n";
		}
	}

	return $out;
}

?>
