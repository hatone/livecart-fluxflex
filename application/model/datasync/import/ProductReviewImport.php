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
ClassLoader::import('application.model.product.ProductReview');
ClassLoader::import('application.controller.backend.ReviewController');

/**
 *  Handles user data import logic
 *
 *  @package application.model.datasync.import
 *  @author Integry Systems
 */
class ProductReviewImport extends DataImport
{
	public function getFields()
	{
		$this->loadLanguageFile('backend/Review');
		$this->loadLanguageFile('backend/Product');

		$controller = new ReviewController($this->application);
		foreach ($controller->getAvailableColumns() as $key => $data)
		{
			$fields[$key] = $this->translate($data['name']);
		}

		unset($fields['Product.name']);

		$groupedFields = $this->getGroupedFields($fields);

		$identify = array();
		foreach (array('ID', 'sku') as $field)
		{
			$field = 'Product.' . $field;
			$identify[$field] = $this->application->translate($field);
		}

		$groupedFields['Product'] = $identify;

		return $groupedFields;
	}

	public function isRootCategory()
	{
		return false;
	}

	protected function getInstance($record, CsvImportProfile $profile)
	{
		$fields = $profile->getSortedFields();
		if (isset($fields['ProductReview']['ID']))
		{
			$instance = ActiveRecordModel::getInstanceByID('ProductReview', $record[$fields['ProductReview']['ID']], true);
		}
		else if (isset($fields['Product']['ID']))
		{
			$parent = ActiveRecordModel::getInstanceByID('Product', $record[$fields['Product']['ID']], true);
		}
		else if (isset($fields['Product']['sku']))
		{
			$parent = Product::getInstanceBySku($record[$fields['Product']['sku']]);
		}
		else
		{
			return;
		}

		if (empty($instance) && empty($parent))
		{
			return;
		}

		if (empty($instance))
		{
			$instance = ProductReview::getNewInstance($parent, User::getNewInstance(''));
			$instance->isEnabled->set(true);
		}

		return $instance;
	}
}

?>
