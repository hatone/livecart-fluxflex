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

require_once (dirname(__FILE__) . '/../Initialize.php');

require_once (FW_DIR . 'request/validator/filter/RegexFilter.php');

/**
 * @package	framework.test
 * @author	Integry Systems
 */
class TestRegexFilter extends UnitTest
{
	public function testFilter()
	{
		$filter = new RegexFilter('[^0-9]');
		$this->assertEqual('123', $filter->apply('z1z2z3'));
	}
}

?>
