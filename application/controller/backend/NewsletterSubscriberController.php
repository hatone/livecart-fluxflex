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

ClassLoader::import('application.controller.backend.abstract.ActiveGridController');
ClassLoader::import('application.model.newsletter.*');

/**
 * Manage newsletters subscribers
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role newsletter
 */
class NewsletterSubscriberController extends ActiveGridController
{
	public function index()
	{
		return $this->setGridResponse(new ActionResponse());
	}

	protected function getClassName()
	{
		return 'NewsletterSubscriber';
	}

	protected function getDefaultColumns()
	{
		return array('NewsletterSubscriber.ID', 'NewsletterSubscriber.email', 'NewsletterSubscriber.isEnabled');
	}
}

?>
