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
ClassLoader::import("application.model.delivery.DeliveryZone");

/**
 * Match an address to delivery zone by address mask string. For example, "5th Avenue" would match all addresses
 * within the 5th Avenue. The address mask usually has to be used together with other masks or state/country
 * rules to make sure an address from a wrong country doesn't get matched.
 *
 * @package application.model.delivery
 * @author Integry Systems <http://integry.com> 
 */
class DeliveryZoneAddressMask extends ActiveRecordModel 
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName("DeliveryZoneAddressMask");
		
		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("deliveryZoneID", "DeliveryZone", "ID", "DeliveryZone", ARInteger::instance()));
		$schema->registerField(new ARField("mask", ARChar::instance(60)));
	}
	
	/**
	 * Gets an existing record instance (persisted on a database).
	 *
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * @return DeliveryZoneAddressMask
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{	
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords, $data);
	}
	
	/**
	 * @return DeliveryZoneState
	 */
	public static function getNewInstance(DeliveryZone $zone, $mask)
	{
	  	$instance = ActiveRecord::getNewInstance(__CLASS__);
	  	
	  	$instance->deliveryZone->set($zone);
	  	$instance->mask->set($mask);
	  	
	  	return $instance;
	}

	/**
	 * @param DeliveryZone $zone
	 * 
	 * @return ARSet
	 */
	public static function getRecordSetByZone(DeliveryZone $zone, $loadReferencedRecords = false)
	{
		$filter = new ARSelectFilter();
		$filter->setCondition(new EqualsCond(new ARFieldHandle(__CLASS__, 'deliveryZoneID'), $zone->getID()));
		
		return self::getRecordSet(__CLASS__, $filter, $loadReferencedRecords);
	}
}
?>
