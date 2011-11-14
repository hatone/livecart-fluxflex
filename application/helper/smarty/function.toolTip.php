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
function smarty_function_toolTip($params, LiveCartSmarty $smarty)
{
	$tip = $params['label'];
	$hint = !empty($params['hint']) ? $params['hint'] : '_tip' . $tip;
	$app = $smarty->getApplication();

	$json = json_encode($app->translate($hint));
	$json = str_replace('<', '\u003C', $json);
	$json = str_replace('>', '\u003E', $json);
	$json = str_replace("'", '\u0027', $json);	
	
	return '<span class="acronym" onmouseover=\'tooltip.show(' . $json. ', 200);\' onmouseout="tooltip.hide();">' . $app->translate($tip) . '</span>';
}

?>
