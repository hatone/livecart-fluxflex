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
 * Smarty block plugin, for generating page menu item
 * This block must always be called in pageMenu block context
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
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_block_menuItem($params, $content, LiveCartSmarty $smarty, &$repeat) 
{
	if ($repeat) 
	{		
		$smarty->clear_assign('menuCaption');
		$smarty->clear_assign('menuAction');
		$smarty->clear_assign('menuPageAction');
	}
	else 
	{
		$item = new HtmlElement('a');
		if ($smarty->get_template_vars('menuAction'))
		{
		  	$href = $smarty->get_template_vars('menuAction');
		}
		else if ($smarty->get_template_vars('menuPageAction'))
		{
		  	$onClick = $smarty->get_template_vars('menuPageAction');
		  	$href = '#';
			$item->setAttribute('onClick', $onClick  . '; return false;');
		}

		$item->setAttribute('href', $href);

		// EXPERIMENTAL - set access key for menu item
		$caption = $smarty->get_template_vars('menuCaption');
		if (FALSE != strpos($caption, '&&'))
		{
		  	$p = strpos($caption, '&&');
		  	$accessKey = substr($caption, $p + 2, 1);
		  	$item->setAttribute('accessKey', $accessKey);
		  	$caption = substr($caption, 0, $p + 3) . '</span>' . substr($caption, $p + 3);
		  	$caption = substr($caption, 0, $p) . '<span class="accessKey">' . substr($caption, $p + 2);		  	
		}
		
		$item->setContent($caption);
		
		$smarty->append('pageMenuItems', $item->render());				
	}
}
?>
