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

ClassLoader::import('application.model.user.UserAddress');

/**
 * Abstract implementation of customer billing or shipping address. A customer can have several
 * billing and shipping addresses.
 *
 * @package application.model.user
 * @author Integry Systems <http://integry.com>
 */
abstract class UserAddressType extends ActiveRecordModel
{
	/**
	 * Define database schema
	 */
	public static function defineSchema($className)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("userID", "user", "ID", 'User', ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("userAddressID", "userAddress", "ID", 'UserAddress', ARInteger::instance()));
	}

	public static function getNewInstance($className, User $user, UserAddress $userAddress)
	{
		$instance = parent::getNewInstance($className);
		$instance->user->set($user);
		$instance->userAddress->set($userAddress);
		return $instance;
	}

	public static function getUserAddress($className, $addressID, User $user)
	{
		$f = new ARSelectFilter();
		$f->setCondition(new EqualsCond(new ARFieldHandle($className, 'ID'), $addressID));
		$f->mergeCondition(new EqualsCond(new ARFieldHandle($className, 'userID'), $user->getID()));
		$s = ActiveRecordModel::getRecordSet($className, $f, array('UserAddress'));

		if (!$s->size())
		{
			throw new ARNotFoundException($className, $addressID);
		}

		return $s->get(0);
	}

	public function save()
	{

		$this->load();

		$this->userAddress->get()->load();
		$this->userAddress->get()->save();
		return parent::save();
	}

	public function serialize()
	{
		return parent::serialize(array('userID'));
	}

	public function __destruct()
	{
		parent::destruct(array('userID', 'userAddressID'));
	}
}

?>
