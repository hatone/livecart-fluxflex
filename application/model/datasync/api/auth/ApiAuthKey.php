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
 * Secret key authorization is a simple method of creating a hash of client and server API keys.
 * There is only one server key, which is defined in LiveCart settings. At this time there are no restrictions of client keys as they are basically only used as a salt for hashing the server key, so the client can use anything as a key.
 *
 * For example:
 * $serverKey = 'secretKey'; // as defined in LiveCart configuration and known to client
 * $clientKey = '1234'; // client can use any string
 * $hash = md5($clientKey . '|' . $serverKey); // $hash = '1031e447b1c76d98122eff5068c69ca5';
 *
 * Example XML:
 *
 * <price>
 *		<get>SKU3554</get>
 *		<auth>
 *			<key>
 *				<client>1234</client>
 *				<hash>1031e447b1c76d98122eff5068c69ca5</hash>
 *			</key>
 *		</auth>
 * </price>
 *
 * @package application.model.datasync.api.auth
 * @author Integry Systems <http://integry.com>
 */
class ApiAuthKey extends ApiAuthorization
{
	public function isValid()
	{
		$params = $this->params['key'];
		$serverKey = $this->application->getConfig()->get('API_SECRET_KEY_SERVER');
		$expectedHash = md5($params['client'] . '|' . $serverKey);

		return $expectedHash == strtolower($params['hash']);
	}
}

?>
