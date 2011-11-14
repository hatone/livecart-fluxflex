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
 * Set canonical URL
 *
 * @package application.helper.smarty
 * @author Integry Systems
 * @see http://googlewebmastercentral.blogspot.com/2009/02/specify-your-canonical.html
 */
function smarty_block_canonical($params, $content, LiveCartSmarty $smarty, &$repeat)
{
	if (!$repeat)
	{
		$parsed = parse_url($content);
		if (!empty($parsed['query']))
		{
			$pairs = array();
			foreach (explode('&amp;', $parsed['query']) as $pair)
			{
				$values = explode('=', $pair, 2);
				if (count($values) != 2)
				{
					continue;
				}

				$pairs[$value[0]] = $value[1];
			}

			$pairs = array_diff_key($pairs, array_flip(array('currency', 'sort')));
			$parsed['query'] = http_build_query($pairs);
		}

		$content = $parsed['path'] . (!empty($parsed['query']) ? '?' . $parsed['query'] : '');

		$GLOBALS['CANONICAL'] = $content;
		$smarty->assign('CANONICAL', $content);
		$smarty->_smarty_vars['CANONICAL'] = $content;
	}
}

?>
