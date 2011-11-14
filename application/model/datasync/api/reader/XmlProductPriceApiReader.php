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
 * ProductPrice API XML format request parsing (reading/routing)
 *
 * @package application.model.datasync
 * @author Integry Systems <http://integry.com>
 */

class XmlProductPriceApiReader extends ApiReader
{
	public static function getXMLPath()
	{
		return '/request/price';
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

			case 'replace':
			case 'set':
				// 'flat' fields
				$request = parent::loadDataInRequest($request,
					self::getXMLPath().'/'.$apiActionName.'/',
					array('sku','currency','definedPrice','definedListPrice')
				);

				// quantity prices
				$quantityPrices = array();
				foreach($this->xml->xpath('/request/price/'.$apiActionName.'/quantityPrices/quantityPrice') as $quantityPrice)
				{
					$quantityPrices[] = array(
						'quantity'=>(string)$quantityPrice->quantity,
						'price'=>(string)$quantityPrice->price,
						'group'=>(string)$quantityPrice->group,
						'currency'=>(string)$quantityPrice->currency
					);
				}
				$request->set('quantityPrices', $quantityPrices);
				break;
		}
		return $request;
	}
}
?>
