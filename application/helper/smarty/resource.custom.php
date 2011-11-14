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
 *  Custom template file handler
 *
 *  Each template file can have 1 or 2 copies - the original template file (not customized) and user-customized
 *  template file. The goal is to avoid the default view file modifications as it can make updates more difficult
 *  (accidently overwritten customizations, etc.), also there would be no need to make the original templates
 *  directory writable to enable template modification from admin interface.
 *
 *  @package application.helper.smarty
 *  @author Integry Systems
 */

function smarty_resource_custom_source($tpl_name, &$tpl_source, LiveCartSmarty $smarty)
{
	if ($path = smarty_custom_get_path($tpl_name, $smarty))
	{
		$tpl_source = file_get_contents($path);
		return true;
	}

	return false;
}

function smarty_resource_custom_timestamp($tpl_name, &$tpl_timestamp, LiveCartSmarty $smarty_obj)
{
	if ($file = smarty_custom_get_path($tpl_name, $smarty_obj))
	{
		$tpl_timestamp = filemtime($file);
		return true;
	}

	return false;
}

function smarty_custom_get_path($tpl_name, LiveCartSmarty $smarty)
{
	$path = null;

	if ('@' != substr($tpl_name, 0, 1))
	{
		$path = $smarty->getApplication()->getRenderer()->getTemplatePath($tpl_name);
	}

	// absolute path
	else
	{
		$path = $smarty->getApplication()->getRenderer()->getBaseTemplatePath($tpl_name);
	}

	return $path;
}

function smarty_resource_custom_secure($tpl_name, LiveCartSmarty $smarty_obj)
{
	// assume all templates are secure
	return true;
}

function smarty_resource_custom_trusted($tpl_name, LiveCartSmarty $smarty_obj)
{
	// not used for templates
}

?>
