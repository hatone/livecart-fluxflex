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
 * Smarty block plugin, for specifying JavaScript action for page menu item
 * This block must always be called in menuItem block context
 *
 * @param array $params
 * @param Smarty $smarty
 * @param $repeat
 *
 * <code>
 *	{pageMenu id="menu"}
 *		{menuItem}
 *			{menuCaption}Click Me{/menuCaption}
 *			{menuAction}http://click.me.com{/menuAction} 
 *		{/menuItem}
 *		{menuItem}
 *			{menuCaption}Another menu item{/menuCaption}
 *			<strong>{pageAction}alert('Somebody clicked on me too!'){/menuAction}</strong> 
 *		{/menuItem}
 *  {/pageMenu}
 * </code>
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_block_pageAction($params, $content, LiveCartSmarty $smarty, &$repeat) 
{	
	if (!$repeat) 
	{		
		$smarty->assign('menuPageAction', $content);
	}
}

?>
