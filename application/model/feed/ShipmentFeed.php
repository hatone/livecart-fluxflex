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

ClassLoader::import('library.activerecord.ARFeed');
ClassLoader::import('application.model.order.Shipment');

/**
 * Order data feed
 *
 * @author Integry Systems
 * @package application.controller
 */
class ShipmentFeed extends ARFeed
{
	protected $productFilter;

	public function __construct(ARSelectFilter $filter, $referencedRecords = array())
	{
		parent::__construct($filter, 'Shipment', array_merge($referencedRecords, array('CustomerOrder')));
	}

	protected function postProcessData()
	{
		$addresses = array();
		foreach ($this->data as $key => $shipment)
		{
			$id = !empty($shipment['shippingAddressID']) ? $shipment['shippingAddressID'] : $shipment['CustomerOrder']['shippingAddressID'];
			$addresses[$id] = $key;
		}

		foreach (ActiveRecordModel::getRecordSetArray('UserAddress', select(in('UserAddress.ID', array_keys($addresses)))) as $address)
		{
			$this->data[$addresses[$address['ID']]]['ShippingAddress'] = $address;
		}
	}
}

?>
