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

ClassLoader::import('application.model.businessrule.RuleAction');
ClassLoader::import('application.model.tax.TaxClass');

/**
 *
 * @author Integry Systems
 * @package application.model.businessrule.action
 */
class RuleActionChangeCurrency extends RuleAction implements RuleOrderAction
{
	public function applyToOrder(CustomerOrder $order)
	{
		$currencyID = $this->getFieldValue('currency', '');
		if($currencyID)
		{
			$currency = Currency::getInstanceById($currencyID);
			$order->changeCurrency($currency);
			ActiveRecordModel::getApplication()->getRequest()->set('currency', $currencyID);
		}
	}

	public function getFields()
	{
		$currencies = ActiveRecordModel::getApplication()->getCurrencyArray();
		return array(array('type' => 'select', 'label' => '_currency', 'name' => 'currency', 'options'=> array_combine($currencies, $currencies)));
	}
}

?>
