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
 * ...
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_uniqid($params, LiveCartSmarty $smarty)
{
	if (isset($params['last']))
	{
		return $smarty->get_template_vars('lastUniqId');
	}
	else
	{
		// start with a letter for XHTML id attribute value compatibility
		$id = 'a' . uniqid();
		$smarty->assign('lastUniqId', $id);

		if (isset($params['assign']))
		{
			$smarty->assign($params['assign'], $id);

			if (!empty($params['noecho']))
			{
				return '';
			}
		}

		return $id;
	}
}

?>
