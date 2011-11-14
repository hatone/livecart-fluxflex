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
 * Creates more complex translation strings that depend on and include numeric variables
 *
 * <code>
 *	  {maketext text="There are [quant,_1,item,items,no items] in your shopping basket." params=$cnt}
 *	  {maketext text="Displaying [_1] to [_2] of [_3] found orders." params=$from,$to,$count}
 * </code>
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_maketext($params, LiveCartSmarty $smarty)
{
	$application = $smarty->getApplication();
	$translation = $application->makeText($params['text'], explode(',', $params['params']));

	if ($application->isTranslationMode() && !isset($params['disableLiveTranslation']))
	{
		$file = $application->getLocale()->translationManager()->getFileByDefKey($params['text']);
		$file = '__file_'.base64_encode($file);
		$translation = '<span class="transMode __trans_' . $params['text'].' '. $file .'">'.$translation.'</span>';
	}

	return $translation;
}

?>
