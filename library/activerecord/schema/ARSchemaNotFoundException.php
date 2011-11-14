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

require_once("schema/ARSchemaException.php");

/**
 * Exception for signaling that a requested schema is not found
 *
 * @package activerecord.schema
 * @author Integry Systems 
 */
class ARSchemaNotFoundException extends ARSchemaException
{
	public function __construct($className)
	{
		parent::__construct("Schema (or class) not found (".$className.")");
	}
}

?>
