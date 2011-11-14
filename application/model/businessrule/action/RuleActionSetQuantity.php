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

/**
 *
 * @author Integry Systems
 * @package application.model.businessrule.action
 */
class RuleActionSetQuantity extends RuleAction implements RuleItemAction
{
	public function applyToItem(BusinessRuleProductInterface $item)
	{
		if ($item instanceof OrderedItem)
		{
			$item->count->set($this->getFieldValue('count'));
			$item->save();
		}
	}

	public function getFields()
	{
		return array(array('type' => 'number', 'label' => '_quantity', 'name' => 'count'));
	}
}

?>
