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

ClassLoader::import('application.model.datasync.api.reader.ApiReader');

/**
 * Stock API XML format request parsing
 *
 * @package application.model.datasync
 * @author Integry Systems <http://integry.com>
 */

class XmlStockApiReader extends ApiReader
{
	public static function getXMLPath()
	{
		return '/request/stock';
	}

	public function loadDataInRequest($request)
	{
		$apiActionName = $this->getApiActionName();
		switch($apiActionName)
		{
			case 'get':
				$request = parent::loadDataInRequest($request, '//', array($apiActionName));
				// rename get to SKU
				$request->set('SKU',$request->get($apiActionName));
				$request->remove($apiActionName);			
				break;

			case 'set':
				// 'flat' fields
				$request = parent::loadDataInRequest($request,
					self::getXMLPath().'/'.$apiActionName.'/',
					array('sku','quantity')
				);
				break;
		}
		return $request;
	}
}
?>
