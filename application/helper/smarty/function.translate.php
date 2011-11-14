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
 * Translates interface text to current locale language
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_translate($params, LiveCartSmarty $smarty)
{
	$application = $smarty->getApplication();

	$key = trim($params['text']);
	$translation = $application->translate($key);
	$translation = preg_replace('/%([a-zA-Z]*)/e', 'smarty_replace_translation_var(\'\\1\', $smarty)', $translation);

	if (!empty($params['noDefault']) && ($translation == $key))
	{
		return '';
	}

	if (!empty($params['eval']))
	{
		$translation = $smarty->evalTpl($translation);
	}

	if ($application->isTranslationMode() && !isset($params['disableLiveTranslation']) && !$application->isBackend())
	{
		$file = $application->getLocale()->translationManager()->getFileByDefKey($params['text']);
		$file = '__file_'.base64_encode($file);
		$translation = '<span class="transMode __trans_' . $params['text'].' '. $file .'">'.$translation.'</span>';
	}

	return $translation;
}

function smarty_replace_translation_var($key, $smarty)
{
	return $smarty->_tpl_vars[$key];
}

?>
