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

ClassLoader::import("application.model.eavcommon.EavSpecificationManagerCommon");
ClassLoader::import("application.model.eav.EavField");
ClassLoader::import("application.model.eav.EavItem");
ClassLoader::import("application.model.eav.EavMultiValueItem");
ClassLoader::import("application.model.eav.EavValue");
ClassLoader::import("application.model.eav.EavStringValue");
ClassLoader::import("application.model.eav.EavNumericValue");
ClassLoader::import("application.model.eav.EavDateValue");

/**
 * Product specification wrapper class. Loads/modifies product specification data.
 *
 * This class usually should not be used directly as most of the attribute manipulations
 * can be done with Product class itself.
 *
 * @package application.model.eav
 * @author Integry Systems <http://integry.com>
 */
class EavSpecificationManager extends EavSpecificationManagerCommon
{
	public function __construct(EavObject $parent, $specificationDataArray = array())
	{
		parent::__construct($parent, $specificationDataArray);
	}

	public function getFieldClass()
	{
		return 'EavField';
	}

	public function getSpecificationFieldSet($loadReferencedRecords = false)
	{
		$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle($this->getFieldClass(), 'classID'), EavField::getClassID($this->owner)));
		if ($this->owner->getStringIdentifier())
		{
			$f->mergeCondition(new EqualsCond(new ARFieldHandle('EavField', 'stringIdentifier'), $this->owner->getStringIdentifier()));
		}

		$f->setOrder(new ARFieldHandle($this->getFieldClass(), 'position'));
		return ActiveRecordModel::getRecordSet($this->getFieldClass(), $f, $loadReferencedRecords);
	}

	public function save()
	{
		$this->owner->save();

		return parent::save();
	}

	/**
	 * Removes persisted product specification property
	 *
	 *	@param EavField $field EavField instance
	 */
	public function removeAttribute(EavField $field)
	{
		return parent::removeAttribute($field);
	}

	public function removeAttributeValue(EavField $field, EavValue $value)
	{
		return parent::removeAttributeValue($field, $value);
	}

	public function isAttributeSet(EavField $field)
	{
		return parent::isAttributeSet($field);
	}

	/**
	 *	Get attribute instance for the particular EavField.
	 *
	 *	If it is a single value selector a EavValue instance needs to be passed as well
	 *
	 *	@param EavField $field EavField instance
	 *	@param EavValue $defaultValue EavValue instance (or nothing if EavField is not selector)
	 *
	 * @return Specification
	 */
	public function getAttribute(EavField $field, $defaultValue = null)
	{
		return parent::getAttribute($field, $defaultValue);
	}

	public static function loadSpecificationForProductArray(&$productArray)
	{
		return parent::loadSpecificationForRecordArray($productArray);
	}

	/**
	 * Load product specification data for a whole array of products at once
	 */
	public static function loadSpecificationForRecordSetArray(&$productArray, $fullSpecification = false)
	{
		return parent::loadSpecificationForRecordSetArray(__CLASS__, $productArray, $fullSpecification);
	}

	protected static function fetchSpecificationData($productIDs, $fullSpecification = false)
	{
		return parent::fetchSpecificationData(__CLASS__, $productIDs, $fullSpecification);
	}
}

?>
