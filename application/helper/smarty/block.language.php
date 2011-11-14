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
 * Language forms
 *
 * @package application.helper.smarty
 * @author Integry Systems
 *
 * @package application.helper.smarty
 */
function smarty_block_language($params, $content, LiveCartSmarty $smarty, &$repeat)
{
	if (!$smarty->getApplication()->getLanguageSetArray())
	{
		return false;
	}

	if ($repeat)
	{
		$smarty->languageBlock = $smarty->getApplication()->getLanguageSetArray();
		$smarty->assign('languageBlock', $smarty->languageBlock);
		$smarty->assign('lang', array_shift($smarty->languageBlock));
		$smarty->langHeadDisplayed = false;

		$user = SessionUser::getUser();
		foreach ($smarty->getApplication()->getLanguageSetArray() as $lang)
		{
			$userPref = $user->getPreference('tab_lang_' . $lang['ID']);
			$isHidden = is_null($userPref) ? !empty($params['hidden']) : $userPref == 'false';
			$classNames[$lang['ID']] = $isHidden ? 'hidden' : '';
		}

		$smarty->langClassNames = $classNames;
	}
	else
	{
		if (!trim($content))
		{
			$repeat = false;
			return false;
		}

		if ($smarty->languageBlock)
		{
			$repeat = true;
		}

		$contentLang = $smarty->get_template_vars('lang');
		$content = '<div class="languageFormContainer languageFormContainer_' . $contentLang['ID'] . ' ' . $smarty->langClassNames[$contentLang['ID']] . '">' . $content . '</div>';

		if (!$smarty->langHeadDisplayed)
		{
			$smarty->assign('langFormId', 'langForm_' . uniqid());
			$smarty->assign('classNames', $smarty->langClassNames);
			$content = $smarty->fetch('block/backend/langFormHead.tpl') . $content;
			$smarty->langHeadDisplayed = true;
		}

		$smarty->assign('lang', array_shift($smarty->languageBlock));

		// form footer
		if (!$repeat)
		{
			$content .= $smarty->fetch('block/backend/langFormFoot.tpl');
		}

		return $content;
	}
}
?>
