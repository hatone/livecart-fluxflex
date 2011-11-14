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
 * @package application.helper
 * @author Integry Systems
 * 
 */
class LiveCartSimpleXMLElement extends SimpleXMLElement
{
	public function addChild($key, $value=null)
	{
		// this version of addChild() escapes $value, 
		$child = parent::addChild($key);
		if($value != null)
		{
			$child[0] = $value;
		}
		return $child;
	}
	
	function Xml2SimpleArray($xml)
	{
        foreach($xml->children() as $b)
        {
                $a = $b->getName();
                if(!$b->children())
                {
                        $arr[$a] = trim($b[0]);
                }
                else{
                        $arr[$a] = self::Xml2SimpleArray($b);
                }
        }
        
        return $arr;
	} 
}
?>
