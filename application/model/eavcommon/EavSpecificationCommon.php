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

ClassLoader::import('application.model.ActiveRecordModel');
ClassLoader::import('application.model.eavcommon.iEavSpecification');

/**
 *
 * @package application.model.eav
 * @author Integry Systems <http://integry.com>
 */
abstract class EavSpecificationCommon extends ActiveRecordModel implements iEavSpecification
{
	abstract public function getValue();

	public function getField()
	{
		return $this->specField;
	}

	public function getFieldInstance()
	{
		return $this->getField()->get();
	}

	public function getOwnerVarName()
	{
		return 'product';
	}

	public function getOwner()
	{
		$field = $this->getOwnerVarName();
		return $this->$field;
	}

	public function setOwner(ActiveRecordModel $owner)
	{
		$field = $this->getOwnerVarName();
		$this->$field->set($owner);
	}

	public function set($value)
	{
		$this->value->set($value);
	}
}

?>
