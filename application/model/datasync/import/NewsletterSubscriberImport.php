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

ClassLoader::import('application.model.datasync.DataImport');
ClassLoader::import('application.model.newsletter.NewsletterSubscriber');
ClassLoader::import('application.controller.backend.UserGroupController');

/**
 *  Handles user data import logic
 *
 *  @package application.model.datasync.import
 *  @author Integry Systems
 */
class NewsletterSubscriberImport extends DataImport
{
	public function getFields()
	{
		$this->loadLanguageFile('backend/NewsletterSubscriber');

		foreach (ActiveGridController::getSchemaColumns('NewsletterSubscriber', $this->application) as $key => $data)
		{
			$fields[$key] = $this->translate($data['name']);
		}

		unset($fields['NewsletterSubscriber.confirmationCode']);

		return $this->getGroupedFields($fields);
	}

	public function isRootCategory()
	{
		return false;
	}

	protected function getInstance($record, CsvImportProfile $profile)
	{
		$fields = $profile->getSortedFields();
		if (isset($fields['NewsletterSubscriber']['ID']))
		{
			$instance = ActiveRecordModel::getInstanceByID('NewsletterSubscriber', $record[$fields['NewsletterSubscriber']['ID']], true);
		}
		else if (isset($fields['NewsletterSubscriber']['email']))
		{
			$instance = NewsletterSubscriber::getInstanceByEmail($record[$fields['NewsletterSubscriber']['email']]);
		}
		else
		{
			return;
		}

		if (empty($instance))
		{
			$instance = NewsletterSubscriber::getNewInstanceByEmail($record[$fields['NewsletterSubscriber']['email']]);
		}

		$this->setLastImportedRecordName($instance->email->get());
		return $instance;
	}
}

?>
