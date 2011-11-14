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
class RuleConditionPaymentMethodIs extends RuleCondition
{
	public function isApplicable()
	{
		if (!$this->getOrder())
		{
			return;
		}

		$method = $this->getOrder()->getPaymentMethod();
		$values = $this->getParam('serializedCondition', array('values' => array()));
		return !empty($values['values'][$method]);
	}
}

?>
