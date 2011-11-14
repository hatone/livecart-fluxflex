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
 * Set page title
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_block_pageTitle($params, $content, LiveCartSmarty $smarty, &$repeat)
{
	if (!$repeat)
	{
		$smarty->assign('TITLE', strip_tags($content));

		if (isset($params['help']))
		{
			$content .= '<script type="text/javascript">Backend.setHelpContext("' . $params['help'] . '")</script>';
		}
		$GLOBALS['PAGE_TITLE'] = $content;

		$smarty->assign('PAGE_TITLE', $content);
		$smarty->_smarty_vars['PAGE_TITLE'] = $content;
	}
}

?>
