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

/**
 * Defines a common interface for different types of specification (attribute) value based filters.
 * These include filtering by numeric or date range or by predefined text values (selectors).
 *
 * @package application.model.filter
 * @author Integry Systems <http://integry.com>
 */
interface SpecificationFilterInterface extends FilterInterface
{
	public function getSpecField();
	public function getFilterGroup();
}

?>
