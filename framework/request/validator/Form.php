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

ClassLoader::import("framework.request.validator.RequestValidator");

/**
 * Request Validator and request data container wrapper
 *
 * @package framework.request.validator
 * @author Integry Systems
 */
class Form
{
	/**
	 * Validator instance
	 *
	 * @var RequestValidator
	 */
	private $validator = null;

	/**
	 * Form data array
	 *
	 * @var array
	 */
	private $data = array();

	private $enableClientSideValidation = true;

	private $params = array();

	public function __construct(RequestValidator $validator)
	{
		$this->validator = $validator;
		if ($validator->hasSavedState())
		{
			$validator->restore();
			$oldRequest = $this->validator->getRestoredRequest();
			if ($oldRequest != null)
			{
				$this->data = $oldRequest->toArray();
			}
		}
	}

	public function setData($data)
	{
		foreach ($data as $name => $value)
		{
			$this->set($name, $value);
		}
	}

	/**
	 * Sets/overwrites a form field value
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		$oldRequest = $this->validator->getRestoredRequest();
		if ($oldRequest != null)
		{
			if (!$oldRequest->isValueSet($name))
			{
				$this->data[$name] = $value;
			}
		}
		else
		{
			$this->data[$name] = $value;
		}
	}

	/**
	 * Gets a form field value
	 *
	 * @param string $fieldName
	 */
	public function get($fieldName)
	{
		if (isset($this->data[$fieldName]))
		{
			return $this->data[$fieldName];
		}
		else
		{
			return null;
		}
	}

	public function getData()
	{
		return $this->data;
	}

	public function getName()
	{
		if ($this->validator != null)
		{
			return $this->validator->getName();
		}
		else
		{
			return null;
		}
	}

	/**
	 * Gets a form validator
	 *
	 * @return RequestValidator
	 */
	public function getValidator()
	{
		return $this->validator;
	}

	public function enableClientSideValidation($flag = true)
	{
		$this->enableClientSideValidation  = $flag;
	}

	public function isClientSideValidationEnabled()
	{
		return $this->enableClientSideValidation;
	}

	public function clearData()
	{
		$this->data = array();
	}

	public function setParam($key, $value)
	{
		$this->params[$key] = $value;
	}

	public function getParams()
	{
		return $this->params;
	}
}

?>
