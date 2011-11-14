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

ClassLoader::import('application.model.businessrule.RuleCondition');

/**
 *
 * @author Integry Systems
 * @package application.model.businessrule.condition
 */
class RuleConditionShippingMethodIs extends RuleCondition
{
	public function isApplicable()
	{
		if (!($this->getOrder() instanceof CustomerOrder))
		{
			return false;
		}

		$values = $this->getParam('serializedCondition', array('values' => array()));

		foreach ($this->getOrder()->getShipments() as $shipment)
		{
			if (!$rate = $shipment->getSelectedRate())
			{
				continue;
			}

			$id = $rate->getServiceID();

			if (!empty($values['values'][$id]))
			{
				return true;
			}
		}
	}
}

?>
