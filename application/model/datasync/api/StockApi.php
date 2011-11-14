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

ClassLoader::import('application.model.datasync.ModelApi');
ClassLoader::import('application.model.product.Product');
ClassLoader::import('application.model.datasync.api.reader.XmlStockApiReader');
ClassLoader::import('application.helper.LiveCartSimpleXMLElement');
	
/**
 * Web service access layer for Stock
 *
 * @package application.model.datasync
 * @author Integry Systems <http://integry.com>
 * 
 */

class StockApi extends ModelApi
{
	private $listFilterMapping = null;
	protected $application;

	public static function canParse(Request $request)
	{
		return parent::canParse($request, array('XmlStockApiReader'));
	}

	public function __construct(LiveCart $application)
	{
		parent::__construct(
			$application,
			null,
			array()
		);
		$this->removeSupportedApiActionName('create','update','list', 'filter', 'delete');
		$this->addSupportedApiActionName('set');
	}

	// ------ 
	public function set()
	{
		$request = $this->getApplication()->getRequest();
		$sku = $request->get('sku');
		$quantity = $request->get('quantity');
		if(is_numeric($quantity) == false)
		{
			throw new Exception('Stock quantity must be numeric');
		}
		$product = Product::getInstanceBySKU($sku);
		if($product == null)
		{
			throw new Exception('Product not found');
		}
		$product->stockCount->set($quantity);
		$product->save();
		return $this->statusResponse($sku, 'updated');
	}
	
	public function get()
	{
		$request = $this->getApplication()->getRequest();
		$product = Product::getInstanceBySku($request->get('SKU'));
		if($product == null)
		{
			throw new Exception('Product not found');
		}
		$response = new LiveCartSimpleXMLElement('<response datetime="'.date('c').'"></response>');		
		$product = $product->toArray();		
		$product['quantity'] = is_numeric($product['stockCount']) == false ? '0' : $product['stockCount'];
		$this->fillSimpleXmlResponseItem($response, $product);
		return new SimpleXMLResponse($response);
	}
	
	public function fillSimpleXmlResponseItem($xml, $product)
	{
		//pp($product);
		$fieldNames = array('sku','quantity');
		foreach($fieldNames as $fieldName)
		{
			$xml->addChild($fieldName, $product[$fieldName]);
		}
	}
}

?>
