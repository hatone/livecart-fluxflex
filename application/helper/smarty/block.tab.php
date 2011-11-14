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
 * Tab
 *
 * @package application.helper.smarty
 * @author Integry Systems
 *
 * @package application.helper.smarty
 */
function smarty_block_tab($params, $content, LiveCartSmarty $smarty, &$repeat)
{
	if (!$repeat)
	{
		ClassLoader::import('application.helper.AccessStringParser');
		if(!empty($params['role']) && !AccessStringParser::run($params['role']))
		{
			return false;
		}

		$user = SessionUser::getUser();
		$userPref = $user->getPreference('tab_' . $params['id']);
		$isHidden = is_null($userPref) ? !empty($params['hidden']) : $userPref == 'false';

		$content = '
<li id="' . $params['id'] . '" class="tab inactive' . ($isHidden ? ' hidden' : '') . '">' . $content . '
	<span> </span>
	<span class="tabHelp">' . $params['help'] . '</span>
</li>';

		return $content;
	}
}
?>
