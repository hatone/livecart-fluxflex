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

/**
 * Data export profiles
 *
 * @package application.model
 * @author Integry Systems <http://integry.com>
 */

class ExportProfile
{
	protected $name;
	protected $className;
	protected $fields = array();

	public function __construction($className)
	{
		$this->className = $className;
	}

	public function setField($key, $field)
	{
		$this->fields[] = array('key' => $key, 'field' => $field);
	}

	public function setFields($fields)
	{
		foreach ($fields as $key => $field)
		{
			$this->setField($key, $field);
		}
	}

	public function getFields()
	{
		return $this->fields;
	}
}

?>
