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
ClassLoader::import('application.model.newsletter.NewsletterSubscriber');

/**
 *
 * @author Integry Systems
 * @package application.model.businessrule.condition
 */
class RuleConditionIsNewsletterSubscriber extends RuleCondition implements RuleOrderCondition
{
	public function isApplicable()
	{
		$user = $this->getContext()->getUser();

		if (!$user)
		{
			return;
		}
		
		if (is_null($user->_isSubscriber))
		{
			$user->load();
			$subscriber = NewsletterSubscriber::getInstanceByEmail($user->email->get());
			if ($subscriber && !$subscriber->isEnabled->get())
			{
				$user->_isSubscriber = false;
			}
			else
			{
				$user->_isSubscriber = true;
			}
		}
		
		return $user->_isSubscriber;
	}

	public static function getSortOrder()
	{
		return 5;
	}
}

?>
