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

ClassLoader::import('application.model.cache.ValueCache');

/**
 *
 *
 * @package application.model.cache
 * @author Integry Systems <http://integry.com>
 */
class MemCachedCache extends ValueCache
{
	private $connection = null;

	public function getName()
	{
		return 'MemCached';
	}

	protected function storeValue($key, $value, $expiration = 0, $namespace = null)
	{
		$connection = $this->getConnection();

		if ($connection)
		{
			if ($namespace)
			{
				$key = $this->getKey($key, $namespace);
				$cntKey = 'namespace_cnt_' . $namespace;

				if (!($cnt = $connection->get($cntKey)))
				{
					$connection->set($cntKey, 0);
				}

				$connection->increment($cntKey);
				$cnt = $connection->get($cntKey);

				$connection->set('namespace_dict_' . $namespace . '_' . $cnt, $key);
			}

			return $connection->set($key, $value, null, $expiration);
		}
	}

	protected function retrieveValue($key, $defaultValue = null, $namespace = null)
	{
		$connection = $this->getConnection();

		if ($connection)
		{
			return $connection->get($this->getKey($key, $namespace));
		}
	}

	public function getNamespace($namespace)
	{
		$connection = $this->getConnection();

		if ($connection)
		{
			$cnt = $connection->get('namespace_cnt_' . $namespace);
			$res = array();
			for ($k = 0; $k <= $cnt; $k++)
			{
				$key = $connection->get('namespace_dict_' . $namespace . '_' . $k);
				$res[$key] = $connection->get($key);
			}

			return $res;
		}
	}

	public function clear($key, $namespace = null)
	{
		$connection = $this->getConnection();

		if ($connection)
		{
			return $connection->delete($this->getKey($key, $namespace));
		}
	}

	public function clearNamespace($key)
	{

	}

	/* do nothing for now */
	public function gc()
	{

	}

	/* always valid */
	public function isValid()
	{
		return $this->getConnection() != false;
	}

	private function getKey($key, $namespace = null)
	{
		return ($namespace ? $namespace . '.' : '') . $key;
	}

	public function getConnection()
	{
		if (!$this->connection)
		{
			$this->connection = new Memcache();
			$this->connection->connect('localhost');
		}

		return $this->connection;
	}
}

?>
