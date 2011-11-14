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

ClassLoader::import('framework.request.validator.RequestValidator');

/**
 *
 * @package application.helper
 * @author Integry Systems
 */
class LiveCartValidator extends RequestValidator
{
	protected $application;
	protected $isPluginProcessed;

	public function setApplication(LiveCart $application)
	{
		$this->application = $application;
	}

	public function isValid()
	{
		$this->processPlugins();
		return parent::isValid();
	}

	public function getJSValidatorParams($requestVarName = null)
	{
		$this->processPlugins();
		return parent::getJSValidatorParams($requestVarName);
	}

	protected function processPlugins()
	{
		if (!$this->isPluginProcessed)
		{
			foreach ($this->application->getPlugins('validator/' . $this->getName()) as $plugin)
			{
				if (!class_exists('ValidatorPlugin', false))
				{
					ClassLoader::import('application.ValidatorPlugin');
				}

				include_once $plugin['path'];
				$inst = new $plugin['class']($this, $this->application);
				$inst->process();
			}
		}

		$this->isPluginProcessed = true;
	}
}

?>
