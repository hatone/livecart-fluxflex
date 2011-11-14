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

ClassLoader::import('application.model.businessrule.action.RuleActionFixedDiscount');

/**
 *
 * @author Integry Systems
 * @package application.model.businessrule.action
 */
class RuleActionFixedSurcharge extends RuleActionFixedDiscount
{
	protected function getDiscountAmount($price)
	{
		return parent::getDiscountAmount($price) * -1;
	}

	public static function getSortOrder()
	{
		return 4;
	}
}

?>
