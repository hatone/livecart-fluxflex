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

ClassLoader::import('framework.renderer.Renderer');

/**
 * Renderer implementation based on raw PHP files ("PHP itself is a templating engine")
 *
 * @package	framework.renderer
 * @author Integry Systems
 */
class PHPRenderer extends Renderer
{
	protected $values = array();

	public function __construct(Application $application)
	{
		$this->application = $application;
	}

	/**
	 * Gets application instance
	 *
	 * @return Smarty
	 */
	public function getApplication()
	{
		return $this->application;
	}

	public function set($name, $value)
	{
		$this->values[$name] = $value;
	}

	public function appendValue($name, $value)
	{
		$this->values[$name] = $value;
	}

	public function setValueArray($array)
	{
		$this->values[$name] = $array;
	}

	public function setObject($name, $object)
	{
		$this->values[$name] = $object;
	}

	public function unsetValue($name)
	{
		unset($this->values[$name]);
	}

	public function unsetAll()
	{
		$this->values = array();
	}

	public function render($view)
	{
		if (file_exists($view))
		{
			ob_start();
			extract($this->values);
			include $view;
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
		else
		{
			throw new ViewNotFoundException($view);
		}
	}

	public function getViewExtension()
	{
		return 'php';
	}
}

?>
