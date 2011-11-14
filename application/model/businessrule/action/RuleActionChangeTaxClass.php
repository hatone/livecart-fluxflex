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
class RuleActionChangeTaxClass extends RuleAction implements RuleItemAction
{
	public function applyToItem(BusinessRuleProductInterface $item)
	{
		$taxClassID = $this->getFieldValue('taxClassID', -1);
		if($taxClassID > 0)
		{
			$product = $item->getProduct();
			if($product instanceof Product)
			{
				// ff('setting custom tax class ID: '.$taxClassID);
				$product->setTemporaryTaxClass(TaxClass::getInstanceByID($taxClassID));
			}
		}
	}

	public function getFields()
	{
		$allTaxClassesArray = array();
		foreach(TaxClass::getAllClasses()->toArray() as $instanceArray)
		{
			$allTaxClassesArray[$instanceArray['ID']] = $instanceArray['name_lang'];
		}
		return array(array('type' => 'select', 'label' => '_tax_class', 'name' => 'taxClassID', 'options'=>$allTaxClassesArray));
	}
}

?>
