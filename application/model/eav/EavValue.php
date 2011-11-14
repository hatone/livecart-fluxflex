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

ClassLoader::import('application.model.eavcommon.EavValueCommon');
ClassLoader::import('application.model.eav.EavItem');

/**
 * Attribute selector value. The same selector value can be assigned to multiple products and usually
 * can be selected from a list of already created values when entering product information - as opposed to
 * input values (numeric or string) that are related to one product only. The advantage of selector values
 * is that they can be used to create product Filters, while input string (SpecificationStringValues) can not.
 *
 * @package application.model.eav
 * @author Integry Systems <http://integry.com>
 */
class EavValue extends EavValueCommon
{
	/**
	 * Define schema in database
	 */
	public static function defineSchema()
	{
		$schema = parent::defineSchema(__CLASS__);
		$schema->registerField(new ARForeignKeyField("fieldID", "EavField", "ID", "EavField", ARInteger::instance()));
	}

	protected function getFieldClass()
	{
		return 'EavField';
	}

	/*####################  Static method implementations ####################*/

	/**
	 *  Get new instance of specification field value
	 *
	 *	@param EavField $field Instance of EavField (must be a selector field)
	 *  @return EavValue
	 */
	public static function getNewInstance(EavField $field)
	{
		return parent::getNewInstance(__CLASS__, $field);
	}

	/**
	 * Get active record instance
	 *
	 * @param integer $recordID
	 * @param boolean $loadRecordData
	 * @param boolean $loadReferencedRecords
	 * @return EavValue
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false)
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords);
	}

	/**
	 * Loads a record set of specification field values belonging to specification field
	 *
	 * @param integer $fieldID
	 * @return ARSet
	 */
	public static function getRecordSet($fieldID)
	{
		return parent::getRecordSet(__CLASS__, $fieldID);
	}

	/**
	 * Loads a record set of specification field values belonging to specification field and returns it as array
	 *
	 * @param integer $fieldID
	 * @return ARSet
	 */
	public static function getRecordSetArray($fieldID)
	{
		return parent::getRecordSetArray(__CLASS__, $fieldID);
	}

	public static function restoreInstance(EavField $field, $valueId, $value)
	{
		return parent::restoreInstance(__CLASS__, $field, $valueId, $value);
	}
}
?>
