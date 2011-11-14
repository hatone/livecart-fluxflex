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
 *  Meta-keywords/description field cleanup
 *
 *  @package application.helper.smarty
 *  @author Integry Systems
 */
function smarty_modifier_meta($value, $default = '')
{
	if (!$value)
	{
		$value = $default;
	}

	$value = preg_replace('/\<script.*\<\/script\>/msU', 'XXX', $value);
	$value = strip_tags($value);
	$value = str_replace(array("\n"), ' ', $value);
	$value = str_replace(" // ", ' ', $value);
	$value = preg_replace('/[ ]+/', ' ', $value);
	$value = trim($value);

	$funcPrefix = function_exists('mb_strlen') ? 'mb_' : '';
	$strlen = $funcPrefix . 'strlen';
	$substr = $funcPrefix . 'substr';

	if ($strlen($value, 'UTF-8') > 163)
	{
		$value = $substr($value, 0, 160, 'UTF-8') . '...';
	}

	return htmlspecialchars($value);
}

?>
