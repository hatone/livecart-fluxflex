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

ClassLoader::import('framework.response.Response');
ClassLoader::import('framework.renderer.Renderable');

/**
 * Class for creating a response that containes action specific data (results) which should be rendered in some way (i.e. TemplateRenderer)
 *
 * @package	framework.response
 * @author Integry Systems
 */
class ActionResponse extends Response implements Renderable
{
	/**
	 * Stores values
	 */
	protected $dataContainer = array();

	/**
	 * Stores value array
	 */
	protected $arrayContainer = array();

	/**
	 * Registered object strorage
	 */
	protected $objectContainer = array();

	public function __construct()
	{
		if (func_num_args())
		{
			foreach (array_chunk(func_get_args(), 2) as $param)
			{
				$this->set($param[0], $param[1]);
			}
		}

		$this->setHeader('Content-type', 'text/html;charset=utf-8');
	}

	/**
	 * A smarter way to set response data - this method automaticaly recognizes type of a
	 * given data and chooses a proper method to register it
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		if (is_object($value))
		{
			$this->setObject($name, $value);
		}
		else
		{
			$this->dataContainer[$name] = $value;
		}
	}

	public function setReference($name, &$value)
	{
		$this->dataContainer[$name] =& $value;
	}

	/**
	 * Gets a response variable name
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function get($name)
	{
		if(isset($this->dataContainer[$name]))
		{
			return $this->dataContainer[$name];
		}
		else if(isset($this->objectContainer[$name]))
		{
			return $this->objectContainer[$name];
		}

		return null;
	}

	/**
	 * Appends variable value (string)
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function appendValue($name, $value)
	{
		if (!empty($this->dataContainer[$name]))
		{
			if (!is_array($value) && !is_array($this->dataContainer[$name]))
			{
				$this->dataContainer[$name] .= $value;
			}
			else
			{
				$arr = array_merge($value, $this->get($name));
				$this->set($name, $arr);
			}
		}
		else
		{
			$this->set($name, $value);
		}
	}

	/**
	 * Registers value array to render
	 *
	 * @param array $array Associative value array
	 * @return void
	 */
	public function setValueArray($array)
	{
		$this->arrayContainer[] = $array;
	}

	/**
	 * Register object to renderer
	 *
	 * @param string $name Name of object
	 * @param object $object Object
	 * @return void
	 */
	public function setObject($name, $object)
	{
		$this->objectContainer[$name] = $object;
	}

	public function render(Renderer $renderer, $view)
	{
		/* Set values */
		foreach($this->dataContainer as $name => $value)
		{
			$renderer->set($name, $value);
		}

		/* Set value arrays */
		foreach($this->arrayContainer as $array)
		{
			$renderer->setValueArray($array);
		}

		/* Set objects */
		foreach($this->objectContainer as $name => $object)
		{
			$renderer->setObject($name, $object);
		}

		/* Render */
		try
		{
			return $renderer->render($view);
		}
		catch(ViewNotFoundException $ex)
		{
			throw $ex;
		}
	}

	public function getData()
	{
		return array_merge($this->dataContainer, $this->arrayContainer, $this->objectContainer);
	}

}

?>
