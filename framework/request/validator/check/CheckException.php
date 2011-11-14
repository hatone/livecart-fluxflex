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

ClassLoader::import("framework.ApplicationException");

/**
 * Exception that is triggered when request variable does not meet requirements defined in a Check subclass
 *
 * @package framework.request.validator.check
 * @author Integry Systems
 */
class CheckException extends ApplicationException
{
}

?>
