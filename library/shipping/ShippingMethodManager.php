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
 * @package library.shipping
 * @author Integry Systems 
 */
class ShippingMethodManager
{
	public static function getHandlerList()
	{
		$ret = array();
		
		foreach (new DirectoryIterator(dirname(__file__) . '/method') as $method)
		{
			if (substr($method->getFileName(), 0, 1) != '.')
			{
				$ret[] = substr($method->getFileName(), 0, -4);
			}
		}
		
		return $ret;	
	}
}

?>
