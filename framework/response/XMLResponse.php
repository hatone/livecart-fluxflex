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

ClassLoader::import("framework.response.ActionResponse");

/**
 * Response rendered as a primitive XML document
 *
 * @package	framework.response
 * @author	Integry Systems
 */
class XMLResponse extends ActionResponse
{
	public function __construct()
	{
		parent::__construct();
		$this->setHeader("Content-Type", "text/xml");
	}
}
?>
