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

include_once dirname(__file__) . '/function.includeCss.php';

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
function smarty_function_loadJs($params, LiveCartSmarty $smarty)
{
	include_once('function.includeJs.php');

	$files = array();

	$files[] = "library/prototype/prototype.js";
	$files[] = "library/livecart.js";
	$files[] = "library/FooterToolbar.js"; // need to be before Frontend.js
	$files[] = "frontend/Frontend.js";
	$files[] = "library/lightbox/lightbox.js";
	$files[] = "library/scriptaculous/scriptaculous.js";
	$files[] = "library/scriptaculous/builder.js";
	$files[] = "library/scriptaculous/effects.js";
	$files[] = "library/scriptaculous/dragdrop.js";
	$files[] = "library/scriptaculous/controls.js";
	$files[] = "library/scriptaculous/slider.js";
	$files[] = "library/scriptaculous/sound.js";

	if (isset($params['form']))
	{
		$files[] = "library/form/Validator.js";
		$files[] = "library/form/ActiveForm.js";
	}

	foreach ($files as $file)
	{
		smarty_function_includeJs(array('file' => $file), $smarty);
	}

	smarty_function_includeCss(array('file' => "library/lightbox/lightbox.css"), $smarty);
}

?>
