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

require_once("ARException.php");

/**
 * Exception for signaling that a requested record (persisted object) was not found
 *
 * @package activerecord
 * @author Integry Systems 
 */
class ARNotFoundException extends ARException
{
	/**
	 * Type (class name) of requested record
	 *
	 * @var string
	 */
	private $className = "";
	private $recordID = "";

	public function __construct($className, $recordID)
	{
		parent::__construct("Instance of $className (ID: $recordID) not found!");
	}
}

?>
