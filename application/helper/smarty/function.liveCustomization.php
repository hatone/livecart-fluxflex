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
function smarty_function_liveCustomization($params, LiveCartSmarty $smarty)
{
	$app = $smarty->getApplication();
	if ($app->isCustomizationMode())
	{
		if (!isset($params['action']))
		{
			include_once('function.includeJs.php');
			include_once('function.includeCss.php');

			smarty_function_includeJs(array('file' => "library/prototype/prototype.js"), $smarty);
			smarty_function_includeJs(array('file' => "library/scriptaculous/scriptaculous.js"), $smarty);
			smarty_function_includeJs(array('file' => "library/livecart.js"), $smarty);
			smarty_function_includeJs(array('file' => "library/form/ActiveForm.js"), $smarty);
			smarty_function_includeJs(array('file' => "library/form/Validator.js"), $smarty);
			smarty_function_includeJs(array('file' => "backend/Backend.js"), $smarty);
			smarty_function_includeJs(array('file' => "frontend/Customize.js"), $smarty);

			smarty_function_includeCss(array('file' => "frontend/LiveCustomization.css"), $smarty);
		}
		else
		{
			$smarty->assign('mode', $app->getCustomizationModeType());
			$smarty->assign('theme', $app->getTheme());

			if ('menu' == $params['action'])
			{
				return $smarty->fetch('customize/menu.tpl');
			}
			else if ('lang' == $params['action'])
			{
				return $smarty->fetch('customize/translate.tpl');
			}
		}
	}
}

?>
