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

ClassLoader::import("framework.request.Request");
ClassLoader::import("framework.request.validator.ValidatorVariable");
ClassLoader::import("framework.request.validator.check.*");
ClassLoader::import("framework.request.validator.filter.*");

/**
 * Request data validator
 *
 * @package framework.request.validator
 * @author Integry Systems
 *
 */
class RequestValidator
{
	/**
	 * Request instance
	 *
	 * @var Request
	 */
	private $request = null;

	/**
	 * The list of validator variables.
	 *
	 * @var ValidatorVariable[]
	 */
	private $validatorVarList = array();

	/**
	 * Validator intance name
	 *
	 * @var string
	 */
	private $name = "";

	/**
	 * List of occurred errors
	 *
	 * @var string[]
	 */
	private $errorList = array();

	/**
	 * Restored request object
	 *
	 * @var Request
	 */
	private $restoredRequest = null;

	/**
	 * Creates a RequestValidator instance
	 *
	 * @param string $name Validator instance name
	 * @param Request $request
	 */
	public function __construct($name, Request $request)
	{
		$this->name = $name;
		$this->request = $request;
	}

	public function getName()
	{
		return $this->name;
	}

	/**
	 * Executes a validator and collects validation errors
	 *
	 */
	private function execute()
	{
		$this->errorList = array();
		foreach ($this->validatorVarList as $var)
		{
			try
			{
				$var->validate();
				//$var->filter();
			}
			catch(CheckException $e)
			{
				$this->errorList[$var->getName()] = $e->getMessage();
			}
		}

		foreach ($this->validatorVarList as $var)
		{
			$var->filter();
		}
	}

	public function getValidatorVar($name)
	{
		if (empty($this->validatorVarList[$name]))
		{
			$this->validatorVarList[$name] = new ValidatorVariable($name, $this->request);
		}
		return $this->validatorVarList[$name];
	}

	public function removeValidatorVar($name)
	{
		unset($this->validatorVarList[$name]);
	}

	public function getChecks()
	{
		$checks = array();

		foreach ($this->validatorVarList as $validatorVar)
		{
			$checks = array_merge($checks, $validatorVar->getChecks());
		}

		return $checks;
	}

	public function getValidatorVars()
	{
		return $this->validatorVarList;
	}

	/**
	 * Applies a check (some kind of requirement for a value) to a request variable
	 *
	 * @param string $varName
	 * @param Check $check
	 */
	public function addCheck($varName, Check $check)
	{
		$this->getValidatorVar($varName)->addCheck($check);
	}

	/**
	 * Assignes a filter to a request variable
	 *
	 * @param string $varName
	 * @param Filter $filter
	 */
	public function addFilter($varName, RequestFilter $filter)
	{
		$this->getValidatorVar($varName)->addFilter($filter);
	}

	/**
	 * Check if submited request data is valid
	 *
	 * @return bool
	 */
	public function isValid()
	{
		$this->execute();

		if (empty($this->errorList))
		{
			return true;
		}
		else
		{
			$this->saveState();
			return false;
		}
	}

	/**
	 * Check if there is a saved request state
	 *
	 * @return bool
	 */
	public function hasSavedState()
	{
		@session_start();
		if (!empty($_SESSION['validatorData'][$this->name]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Store request object and error list in session
	 *
	 */
	public function saveState()
	{
		//@session_start();
		$_SESSION['validatorData'][$this->name]['error'] = $this->errorList;
		$_SESSION['validatorData'][$this->name]['data'] = $this->request->toArray();
		unset($_SESSION['validatorData'][$this->name]['data']['server']);
	}

	/**
	 * Restores request object and errors list from session (restore form variables)
	 *
	 */
	public function restore()
	{
		@session_start();

		$this->errorList = $_SESSION['validatorData'][$this->name]['error'];
		//$this->restoredData =  $_SESSION['validatorData'][$this->name]['data'];
		$this->restoredRequest = new Request();
		$this->restoredRequest->setValueArray($_SESSION['validatorData'][$this->name]['data']);

		unset($_SESSION['validatorData'][$this->name]);
	}

	/**
	 * Get restored request object
	 *
	 * @return Request
	 */
	public function getRestoredRequest()
	{
		return $this->restoredRequest;
	}

	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Get errors list
	 *
	 * @return string[]
	 */
	public function getErrorList()
	{
		return $this->errorList;
	}

	public function getJSValidatorParams($requestVarName = null)
	{
		if ($requestVarName != null)
		{
			return $this->encode($this->getValidatorVar($requestVarName)->getCheckData());
		}
		else
		{
			$validatorData = array();
			foreach ($this->validatorVarList as $name => $var)
			{
				$validatorData[$name] = $var->getCheckData();
			}
			return $this->encode($validatorData);
		}
	}

	public function getJSFilterParams($requestVarName = null)
	{
		if ($requestVarName != null)
		{
			return $this->encode($this->getValidatorVar($requestVarName)->getFilterData());
		}
		else
		{
			$validatorData = array();
			foreach ($this->validatorVarList as $name => $var)
			{
				$validatorData[$name] = $var->getFilterData();
			}
			return $this->encode($validatorData);
		}
	}

	public function triggerError($fieldName, $errorMessage)
	{
		$this->errorList[$fieldName] = $errorMessage;
	}

	protected function encode($data)
	{
		return str_replace('"', "&quot;", json_encode($data));
	}
}

?>
