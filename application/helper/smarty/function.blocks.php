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
 * Renders and displays a page content block
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_blocks($params, LiveCartSmarty $smarty)
{
	$app = $smarty->getApplication();

	$blocks = explode(' ', trim(preg_replace('/\s+/', ' ', $params['blocks'])));
	$blocks = $app->getRenderer()->sortBlocks($blocks);

	$out = array();
	foreach ($blocks as $block)
	{
		$out[$block] = $app->getBlockContent($block);
	}
	$content = implode("\n", $out);

	if (!empty($params['id']))
	{
		$smarty->set('CONTENT', $content);
		$parent = $app->getBlockContent($params['id']);
		if ($parent)
		{
			$content = $parent;
		}
	}

	return $content;
}

?>
