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
 * Defines common interface for different types of filters.
 * It makes it possible to filter products not only by their attribute values, but also by price, manufacturer,
 * keyword search, etc.
 *
 * @package application.model.filter
 * @author Integry Systems <http://integry.com>
 */
interface FilterInterface
{
	/**
	 * Create an ActiveRecord Condition object to use for product selection
	 *
	 * @return Condition
	 */
	public function getCondition();
	
	/**
	 *	Adds JOIN definition to ARSelectFilter to retrieve product attribute value for the particular Filter
	 *	
	 *	@param	ARSelectFilter	$filter	Filter instance
	 */
	public function defineJoin(ARSelectFilter $filter);	
}

?>
