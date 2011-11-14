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
 * State assignment to a DeliveryZone 
 *
 * @package application.model.delivery
 * @author Integry Systems <http://integry.com> 
 */
class DeliveryZoneState extends ActiveRecordModel 
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName("DeliveryZoneState");
		
		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("deliveryZoneID", "DeliveryZone", "ID", "DeliveryZone", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("stateID", "State", "ID", "State", ARInteger::instance()));
	}

	/**
	 * @return DeliveryZoneState
	 */
	public static function getNewInstance(DeliveryZone $zone, State $state)
	{
	  	$instance = ActiveRecord::getNewInstance(__CLASS__);
	  	
	  	$instance->deliveryZone->set($zone);
	  	$instance->state->set($state);
	  	
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

	public static function removeByZone(DeliveryZone $zone)
	{
		$filter = new ARDeleteFilter();
		$filter->setCondition(new EqualsCond(new ARFieldHandle(__CLASS__, 'deliveryZoneID'), $zone->getID()));
		
		return ActiveRecord::deleteRecordSet(__CLASS__, $filter);
	}
}

?>
