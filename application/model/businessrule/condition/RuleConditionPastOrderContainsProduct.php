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

ClassLoader::import('application.model.businessrule.condition.RuleConditionContainsProduct');
ClassLoader::import('application.model.businessrule.interface.RuleOrderCondition');

/**
 *
 * @author Integry Systems
 * @package application.model.businessrule.condition
 */
class RuleConditionPastOrderContainsProduct extends RuleConditionContainsProduct
{
	protected function getOrders()
	{
		$ser = $this->params['serializedCondition'];
		if (!empty($ser['time']))
		{
			$t = $ser['time'];
			$timeFrom = $timeTo = time();
			if (empty($t['conditionTime']) || ('before' == $t['conditionTime']))
			{
				$multi = array('min' => 60, 'hr' => 3600, 'day' => 3600 * 24, 'year' => 3600 * 24 * 365);
				foreach ($multi as $key => $seconds)
				{
					if (!empty($t[$key]))
					{
						$timeFrom -= $t[$key] * $seconds;
					}
				}
			}
			else
			{
				if (!empty($t['from']))
				{
					$timeFrom = strtotime($t['from']);
				}

				if (!empty($t['to']))
				{
					$timeTo = strtotime($t['to']);
				}
			}
		}
		else
		{
			$timeTo = time();
			$timeFrom = 0;
		}

		$pastOrders = $this->getContext()->getPastOrdersBetween($timeFrom, $timeTo);

		return $pastOrders;
	}

	public static function getSortOrder()
	{
		return 20;
	}
}

?>
