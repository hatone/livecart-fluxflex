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
 * Smarty block plugin, for generating page menus
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
 *			{pageAction}alert('Somebody clicked on me too!'){/menuAction}
 *		{/menuItem}
 *  {/pageMenu}
 * </code>
 *
 * @return string Menu HTML code
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_block_pageMenu($params, $content, LiveCartSmarty $smarty, &$repeat) 
{	
	if ($repeat) 
	{		
		$smarty->clear_assign('pageMenuItems');
	}
	else 
	{
		$items = $smarty->get_template_vars('pageMenuItems');

		$menuDiv = new HtmlElement('div');
		$menuDiv->setAttribute('id', $params['id']);
		$menuDiv->setAttribute('tabIndex', 1);
		$menuDiv->setContent(implode(' | ', $items));
		
		return $menuDiv->render();
	}
	
}

?>
