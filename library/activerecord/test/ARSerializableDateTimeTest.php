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

require_once dirname(__FILE__) . '/Initialize.php';

ClassLoader::import("library.activerecord.ARSerializableDateTime");

/**
 * @author Integry Systems
 * @package test.activerecord
 */
class ARSerializableDateTimeTest extends UnitTest
{
	public function getUsedSchemas()
	{
		return array();
	}

	public function testSerializeDate()
	{
		$myBirthday = new ARSerializableDateTime('1985-01-30');

		$myBirthdaySerialized = serialize($myBirthday);
		$myBirthday = unserialize($myBirthdaySerialized);

		$this->assertEqual($myBirthday->format('Y-m-d'), '1985-01-30');
	}

	public function testSerializeCurrentDate()
	{
		$currentDate = new ARSerializableDateTime();

		$currentDateSerialized = serialize($currentDate);
		$currentDate = unserialize($currentDateSerialized);

		$this->assertEqual($currentDate->format('Y-m-d'), date('Y-m-d'));
	}

	public function testSerializeNullDate()
	{
		$nullDate = new ARSerializableDateTime(null);

		$nullDateSerialized = serialize($nullDate);
		$nullDate = unserialize($nullDateSerialized);

		$this->assertEqual($nullDate->format('Y-m-d'), null);
		$this->assertTrue($nullDate->isNull());
	}
}
?>
