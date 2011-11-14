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
ClassLoader::import('application.model.sitenews.NewsPost');
ClassLoader::import('application.controller.backend.UserGroupController');

/**
 *  Handles user data import logic
 *
 *  @package application.model.datasync.import
 *  @author Integry Systems
 */
class NewsPostImport extends DataImport
{
	public function getFields()
	{
		$this->loadLanguageFile('backend/SiteNews');

		foreach (ActiveGridController::getSchemaColumns('NewsPost', $this->application) as $key => $data)
		{
			$fields[$key] = $this->translate($data['name']);
		}

		return $this->getGroupedFields($fields);
	}

	public function isRootCategory()
	{
		return false;
	}

	protected function getInstance($record, CsvImportProfile $profile)
	{
		$fields = $profile->getSortedFields();
		if (isset($fields['NewsPost']['ID']))
		{
			try
			{
				$instance = ActiveRecordModel::getInstanceByID('NewsPost', $record[$fields['NewsPost']['ID']], true);
			}
			catch (ARNotFoundException $e)
			{

			}
		}

		if (empty($instance))
		{
			$instance = ActiveRecordModel::getNewInstance('NewsPost');
		}

		//$this->setLastImportedRecordName($instance->getID());
		return $instance;
	}
}

?>
