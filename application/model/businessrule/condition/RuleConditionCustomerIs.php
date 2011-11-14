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
ClassLoader::import('application.model.businessrule.interface.RuleOrderCondition');

/**
 *
 * @author Integry Systems
 * @package application.model.businessrule.condition
 */
class RuleConditionCustomerIs extends RuleCondition implements RuleOrderCondition
{
	public function isApplicable()
	{
		$user = $this->getContext()->getUser();

		if (!$user)
		{
			return;
		}

		$userGroup = $user->userGroup->get();
		$userID = $user->getID();
		$userGroupID = $userGroup ? $userGroup->getID() : null;

		foreach ($this->records as $record)
		{
			if ((!empty($record['userID']) && ($record['userID'] == $userID)) || (!empty($record['userGroupID']) && ($record['userGroupID'] == $userGroupID)))
			{
				return true;
			}
		}
	}

	public static function getSortOrder()
	{
		return 5;
	}
}

?>
