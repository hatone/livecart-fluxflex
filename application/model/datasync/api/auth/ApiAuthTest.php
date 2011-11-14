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

ClassLoader::import('application.model.datasync.api.ApiAuthorization');

/**
 * Dummy authorization method (for testing purposes, does not require any authentication credentials)
 *
 * @package application.model.datasync.api.auth
 * @author Integry Systems <http://integry.com>
 */
class ApiAuthTest extends ApiAuthorization
{
	public function isValid()
	{
		return true;
	}
}

?>
