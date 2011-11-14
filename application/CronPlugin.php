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
 *  Cron (scheduled) task
 *
 *  @package application
 *  @author Integry Systems
 */
abstract class CronPlugin
{
	protected $application;
	protected $path;

	abstract public function process();

	public function __construct(LiveCart $application, $path)
	{
		$this->application = $application;
		$this->path = $path;
	}

	public function isExecutable($interval)
	{
		return time() - $this->getLastExecutionTime() >= $interval;
	}

	public function getLastExecutionTime()
	{
		$file = $this->getExecutionTimeFile();
		return !file_exists($file) ? null : include $file;
	}

	public function markCompletedExecution()
	{
		$file = $this->getExecutionTimeFile();
		$dir = dirname($file);
		if (!file_exists($dir))
		{
			mkdir($dir, octdec(777), true);
		}

		file_put_contents($file, '<?php return ' . time() . '; ?>');
	}

	private function getExecutionTimeFile()
	{
		return ClassLoader::getRealPath('storage.configuration.cron.') . md5($this->path) . '.php';
	}
}

?>
