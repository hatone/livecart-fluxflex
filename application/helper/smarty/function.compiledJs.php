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
 * ...
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_compiledJs($params, LiveCartSmarty $smarty)
{
	$includedJavascriptTimestamp = $smarty->_smarty_vars["INCLUDED_JAVASCRIPT_TIMESTAMP"];
	$includedJavascriptFiles = $smarty->_smarty_vars["INCLUDED_JAVASCRIPT_FILES"];

	if($includedJavascriptFiles && isset($params['glue']) && ($params['glue'] == 'true') && !$smarty->getApplication()->isDevMode() && !$smarty->getApplication()->isTemplateCustomizationMode())
	{
		$request = $smarty->getApplication()->getRequest();

		if (isset($params['nameMethod']) && 'hash' == $params['nameMethod'])
		{
			$names = array_keys($includedJavascriptFiles);
			sort($names);
			$compiledFileName = md5(implode("\n", $names)) . '.js';
		}
		else
		{
			$compiledFileName = $request->getControllerName() . '-' . $request->getActionName() . '.js';
		}

		$compiledFilePath = ClassLoader::getRealPath('public.cache.javascript.') .  $compiledFileName;
		$baseDir = ClassLoader::getRealPath('public.javascript.');

		$compiledFileTimestamp = 0;
		if(!is_file($compiledFilePath) || filemtime($compiledFilePath) < $includedJavascriptTimestamp)
		{
			if(!is_dir(ClassLoader::getRealPath('public.cache.javascript')))
			{
				mkdir(ClassLoader::getRealPath('public.cache.javascript'), 0777, true);
			}

			// compile
			$compiledFileContent = "";
			$compiledFilesList = array();
			foreach($includedJavascriptFiles as $jsFile => $fileName)
			{
				$compiledFileContent .= "\n\n\n/***************************************************\n";
				$compiledFileContent .= " * " . str_replace($baseDir, '', $jsFile) . "\n";
				$compiledFileContent .= " ***************************************************/\n\n";

				$compiledFileContent .= file_get_contents($jsFile);
				$compiledFilesList[] = basename($jsFile);
			}

			file_put_contents($compiledFilePath, $compiledFileContent);

			if (function_exists('gzencode'))
			{
				file_put_contents($compiledFilePath . '.gz', gzencode($compiledFileContent, 9));
			}
		}

		$compiledFileTimestamp = filemtime($compiledFilePath);

		return '<script src="gzip.php?file=' . $compiledFileName . '&amp;time=' . $compiledFileTimestamp . '" type="text/javascript"></script>';
	}
	else if ($includedJavascriptFiles)
	{
		$includeString = "";
		$publicPath = ClassLoader::getRealPath('public.');
		foreach($includedJavascriptFiles as $path => $jsFile)
		{
			$urlPath = str_replace('\\', '/', str_replace($publicPath, '', $jsFile));
			$includeString .= '<script src="' .$urlPath . '?' . filemtime($path) . '" type="text/javascript"></script>' . "\n";
		}

		return $includeString;
	}
}

?>
