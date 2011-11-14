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
 *
 *
 *  @package application.helper.smarty
 *  @author Integry Systems
 */
function smarty_modifier_mark_substring($string, $substring, $start = '<strong>', $end = '</strong>')
{
	$substring = preg_replace(
		array('/\//', '/\^/', '/\./', '/\$/', '/\|/', '/\(/', '/\)/', '/\[/', '/\]/', '/\*/', '/\+/', '/\?/', '/\{/', '/\}/', '/\,/'),
		array('\/', '\^', '\.', '\$', '\|', '\(', '\)', '\[', '\]', '\*', '\+', '\?', '\{', '\}', '\,'),
		$substring
	);
	return preg_replace('/('.$substring.')/i', $start.'$0'.$end, $string);
}

?>
