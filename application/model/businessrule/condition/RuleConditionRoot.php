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
 * Main condition constraints - check if enabled, date interval, order coupons
 *
 * @author Integry Systems
 * @package application.model.businessrule.condition
 */
class RuleConditionRoot extends RuleCondition
{
	public function isApplicable()
	{
		if (empty($this->params['isEnabled']))
		{
			return false;
		}

		if (!empty($this->params['couponCode']))
		{
			if (!$this->getContext()->getOrder() || !$this->getContext()->getOrder()->hasCoupon($this->params['couponCode']))
			{
				return false;
			}
		}

		if (!empty($this->params['validFrom']))
		{
			if (strtotime($this->params['validFrom']) > time())
			{
				return false;
			}
		}

		if (!empty($this->params['validTo']))
		{
			if (strtotime($this->params['validTo']) < time())
			{
				return false;
			}
		}

		return true;
	}
}

?>
