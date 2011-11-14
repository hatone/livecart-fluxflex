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

ClassLoader::import('library.activerecord.ARSerializableDateTime');

/**
 * @author Integry Systems
 * @package test.activerecord
 */
class SerializeTest extends UnitTest
{
	public function getUsedSchemas()
	{
		return array();
	}

	public function setUp()
	{
		ActiveRecordModel::executeUpdate('DROP TABLE IF EXISTS SerializedModel');

		ActiveRecordModel::executeUpdate('
			CREATE TABLE SerializedModel (
			ID INTEGER UNSIGNED NOT NULL,
			name VARCHAR(60) NOT NULL,
			CONSTRAINT PK_Manufacturer PRIMARY KEY (ID))');

		return parent::setUp();
	}

	public function tearDown()
	{
		ActiveRecordModel::executeUpdate('DROP TABLE SerializedModel');
		return parent::tearDown();
	}

	public function testSerializeSpeed()
	{
		for ($k = 1; $k <= 10; $k++)
		{
			$record = ActiveRecord::getNewInstance('SerializedModel');
			$record->setID($k);
			$record->name->set('some name ' . $k);
			$record->save();
		}

		ActiveRecord::clearPool();

		// fetch from database
		$fetchTime = microtime(true);
		$set = ActiveRecord::getRecordSet('SerializedModel', new ARSelectFilter());
		$fetchTime = microtime(true) - $fetchTime;

		$serialized = serialize($set);
		ActiveRecord::clearPool();

		// unserialize
		$serTime = microtime(true);
		$set = unserialize($serialized);
		$serTime = microtime(true) - $serTime;

		$this->assertTrue($serTime < $fetchTime);
	}
}

class SerializedModel extends ActiveRecord
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField('ID', ARInteger::instance()));
		$schema->registerField(new ARField('name', ARVarchar::instance(60)));
	}
}

?>
