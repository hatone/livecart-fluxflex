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
ClassLoader::import("framework.roles.RolesParser");

/**
 * @package	framework.roles
 * @author	Integry Systems
 */
class RolesDirectoryParser
{
	private $fileFilters;
	private $directoryPath;
	private $cacheDirectoryPath;
	private $classParsers = array();

	public function __construct($directoryPath, $cacheDirectoryPath, $fileFilters = array('/\w+Controller\.php$/'))
	{
		$this->fileFilters = $fileFilters;
		$this->directoryPath = $directoryPath;
		$this->cacheDirectoryPath = $cacheDirectoryPath;

		if(!is_dir($this->directoryPath))
		{
			throw new ApplicationException("Invalid directory path: {$this->directoryPath}");
		}
		else if(!is_writable($this->cacheDirectoryPath . DIRECTORY_SEPARATOR))
		{
			throw new ApplicationException("Could not write to cache directory {$this->cacheDirectoryPath}");
		}
		else
		{
			$this->parseDirectory();
			$this->cache();
		}
	}

	public function getClassParsers()
	{
		return $this->classParsers;
	}

	public function cache()
	{
		foreach($this->getClassParsers() as $classParser)
		{
			if($classParser->isExpired())
			{
				$classParser->cache();
			}
		}
	}

	private function parseDirectory()
	{
		if ($dir = opendir($this->directoryPath)) {
			while (($controllerFile = readdir($dir)) !== false) {

				if(!$this->matchFilters($controllerFile)) continue;

				$controllerName = substr($controllerFile, 0, -4);
				$this->classParsers[] = new RolesParser(
					$this->directoryPath . DIRECTORY_SEPARATOR . $controllerFile,
					$this->cacheDirectoryPath . DIRECTORY_SEPARATOR . $controllerName . 'Roles.php'
				);
			}

			closedir($dir);
		}
		else
		{
			throw new ApplicationException("Could not read from directory: {$this->directoryPath}");
		}
	}

	private function matchFilters($file)
	{
		$matchedFilters = true;
		foreach($this->fileFilters as $filter)
		{
			if(!preg_match($filter, $file))
			{
				$matchedFilters = false;
				break;
			}
		}

		return $matchedFilters;
	}
}
?>
