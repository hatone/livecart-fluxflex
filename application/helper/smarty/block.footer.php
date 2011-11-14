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
 * Display page section header and footer only if content is present
 * This helps to avoid using redundant if's
 *
 * @param array $params
 * @param Smarty $smarty
 * @param $repeat
 *
 * <code>
 *	{sect}
 *		{header}
 *			Section header
 *		{/header}
 *		{content}
 *			Section content
 *		{/content}
 *		{footer}
 *			Section footer
 *		{/footer}
 *  {/sect}
 * </code>
 *
 * @return string Section HTML code
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_block_footer($params, $content, LiveCartSmarty $smarty, &$repeat)
{
	if (!$repeat)
	{
		$counter = $smarty->get_template_vars('sectCounter');
		$blocks = $smarty->get_template_vars('sect');
		$blocks[$counter]['footer'] = $content;
		$smarty->assign('sect', $blocks);
	}
}

?>
