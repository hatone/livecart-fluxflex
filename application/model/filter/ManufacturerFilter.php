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

ClassLoader::import('application.model.filter.FilterInterface');
ClassLoader::importNow('application.helper.CreateHandleString');

/**
 * Filter product list by manufacturer. The manufacturer filters are generated automatically.
 *
 * @package application.model.filter
 * @author Integry Systems <http://integry.com>
 */
class ManufacturerFilter implements FilterInterface
{
	private $manufacturerID = 0;

	private $manufacturerName = '';

	function __construct($manufacturerID, $manufacturerName)
	{
		$this->manufacturerID = $manufacturerID;
		$this->manufacturerName = $manufacturerName;
	}

	public function getCondition()
	{
		return new EqualsCond(new ARFieldHandle('Product', 'manufacturerID'), $this->manufacturerID);
	}

	public function defineJoin(ARSelectFilter $filter)
	{
		/* do nothing */
	}

	public function getID()
	{
		return 'm' . $this->manufacturerID;
	}

	public function getName()
	{
		return $this->manufacturerName;
	}

	public function getManufacturerID()
	{
		return $this->manufacturerID;
	}

	public function toArray()
	{
		$array = array();
		$array['name_lang'] = $this->manufacturerName;
		$array['handle'] = createHandleString($array['name_lang']);
		$array['ID'] = $this->getID();
		return $array;
	}
}

?>
