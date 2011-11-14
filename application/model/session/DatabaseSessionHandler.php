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

ClassLoader::import('application.model.session.SessionHandler');
ClassLoader::import('application.model.session.SessionData');

/**
 * Session storage and retrieval from database
 *
 * @package application.model.session
 * @author Integry Systems
 */
class DatabaseSessionHandler extends SessionHandler
{
	const KEEPALIVE_INTERVAL = 60;

	protected $db;
	protected $isExistingSession;
	protected $id;
	protected $originalData;

	public function open()
	{
		try
		{
			$this->db = ActiveRecordModel::getDBConnection();
			$this->db->sessionHandler = $this;
			return true;
		}
		catch (SQLException $e)
		{
			return false;
		}
	}

	public function close()
	{
		return true;
	}

	public function read($id)
	{
		if (!$this->db)
		{
			return;
		}

		try
		{
			$data = ActiveRecordModel::getRecordSetArray('SessionData', select(eq('SessionData.ID', $id)));
		}
		catch (SQLException $e)
		{
			return '';
		}

		$this->isExistingSession = count($data) > 0;

		if ($data)
		{
			$data = array_shift($data);
			$this->originalData = $data['data'];

			if ((time() - $data['lastUpdated'] > self::KEEPALIVE_INTERVAL) || (!$data['userID'] && !$data['cacheUpdated']))
			{
				$this->forceUpdate = true;
			}

			$this->id = $data['ID'];
			$this->userID = $data['userID'];
			$this->cacheUpdated = $data['cacheUpdated'];

			return $data['data'];
		}

		return '';
	}

	public function write($id, $data)
	{
		try
		{
			if ($this->isExistingSession)
			{
				if (($this->originalData != $data) || $this->forceUpdate)
				{
					SessionData::updateData($id, $data, $this->userID, $this->cacheUpdated, $this->db);
				}
			}
			else
			{
				SessionData::insertData($id, $data,  $this->userID, $this->cacheUpdated, $this->db);
			}

			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function destroy($id)
	{
		try
		{
			$inst = ActiveRecordModel::getInstanceByID('SessionData', $id, true);
			$inst->delete();
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function gc($max)
	{
		SessionData::deleteSessions($max);
	}
}

?>
