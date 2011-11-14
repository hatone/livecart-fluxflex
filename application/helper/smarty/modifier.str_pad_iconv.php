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

include_once dirname(__file__) . '/modifier.str_pad_left.php';

/**
 *
 *
 *  @package application.helper.smarty
 *  @author Integry Systems
 */
function smarty_modifier_str_pad_iconv($string, $count, $pad = ' ', $pad_type = STR_PAD_RIGHT)
{
	return iconv_str_pad($string, $count, $pad, $pad_type);
}

?>
