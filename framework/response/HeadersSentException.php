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

ClassLoader::import('framework.response.ResponseException');

/**
 * ...
 *
 * @package		framework.response
 * @author	Integry Systems
 */
class HeadersSentException extends ResponseException 
{
	/**
	 * Constructs object
	 */
	public function __construct() 
  {
		$file = null;
		$line = null;
		headers_sent($file, $line);
		
		parent::__construct("Headers have been already sent in ($file) on line $line");
	}
}

?>
