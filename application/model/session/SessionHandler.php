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
 * @package application.model.session
 * @author Integry Systems
 */
abstract class SessionHandler
{
	protected $forceUpdate;
	protected $userID;
	protected $cacheUpdated;
	protected $lastUpdated;

	public abstract function open();
	public abstract function close();
	public abstract function read($id);
	public abstract function write($id, $data);
	public abstract function destroy($id);
	public abstract function gc($max);

	public function setHandlerInstance()
	{
		//return true;
		session_set_save_handler(array($this, 'open'),
								 array($this, 'close'),
								 array($this, 'read'),
								 array($this, 'write'),
								 array($this, 'destroy'),
								 array($this, 'gc'));
	}

	public function setUser(User $user)
	{
		$id = $user->getID();

		if ($id != $this->userID)
		{
			$this->userID = $id;
			$this->forceUpdate = true;
		}
	}

	public function updateCacheTimestamp()
	{
		$this->cacheUpdated = time();
		$this->forceUpdate = true;
	}

	public function getCacheUpdateTime()
	{
		return $this->cacheUpdated;
	}
}

?>
