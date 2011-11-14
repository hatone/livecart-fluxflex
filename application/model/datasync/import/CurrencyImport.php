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
ClassLoader::import('application.model.Currency');
ClassLoader::import('application.controller.backend.UserGroupController');

/**
 *  Handles user data import logic
 *
 *  @package application.model.datasync.import
 *  @author Integry Systems
 */
class CurrencyImport extends DataImport
{
	public function getFields()
	{
		$this->loadLanguageFile('backend/Currency');

		foreach (ActiveGridController::getSchemaColumns('Currency', $this->application) as $key => $data)
		{
			$fields[$key] = $this->translate($data['name']);
		}

		unset($fields['Currency.position']);
		unset($fields['Currency.lastUpdated']);
		unset($fields['Currency.rounding']);

		return $this->getGroupedFields($fields);
	}

	public function isRootCategory()
	{
		return false;
	}

	protected function getInstance($record, CsvImportProfile $profile)
	{
		$fields = $profile->getSortedFields();
		if (isset($fields['Currency']['ID']))
		{
			try
			{
				$instance = Currency::getInstanceByID($record[$fields['Currency']['ID']], true);
			}
			catch (ARNotFoundException $e)
			{

			}
		}
		else
		{
			return;
		}

		if (empty($instance))
		{
			$instance = Currency::getNewInstance($record[$fields['Currency']['ID']]);
		}

		$this->setLastImportedRecordName($instance->getID());
		return $instance;
	}
}

?>
