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
function smarty_function_img($params, LiveCartSmarty $smarty) 
{
	if(isset($params['src']) && (substr($params['src'], 0, 6) == 'image/' || substr($params['src'], 0, 7) == 'upload/'))
	{
		if(is_file($params['src']))
		{
			$imageTimestamp = @filemtime(ClassLoader::getRealPath('public.') . str_replace('/',DIRECTORY_SEPARATOR, $params['src']));
			$params['src'] .= '?' . $imageTimestamp;
		}
	}
	
	$content = "<img ";
	foreach($params as $name => $value)
	{
		$content .= $name . '="' . $value . '" ';
	}
	$content .= "/>";
	
	return $content;
}
?>
