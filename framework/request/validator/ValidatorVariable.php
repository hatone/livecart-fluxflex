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
 * Maps request variable to checks and filters (validation elements)
 *
 * Note: this class is protected and should not be used directly. Use a RequestValidator
 * instead.
 *
 * @see RequestValidator
 * @package framework.request.validator
 * @author Integry Systems
 */
class ValidatorVariable
{
	/**
	 * List of Check subclass objects
	 *
	 * @var Check[]
	 */
	private $checkList = array();

	/**
	 * List of Filter subclass objects
	 *
	 * @var RequestFilter[]
	 */
	private $filterList = array();

	/**
	 * Request variable name that is going to be mapped to checks and filters
	 *
	 * @var string
	 */
	private $varName = "";

	/**
	 * Request instance
	 *
	 * @var Request
	 */
	private $request = null;

	public function __construct($varName, Request $request)
	{
		$this->varName = $varName;
		$this->request = $request;
	}

	public function addCheck(Check $check)
	{
		$this->checkList[] = $check;
	}

	public function addFilter(RequestFilter $filter)
	{
		$this->filterList[] = $filter;
	}

	public function getName()
	{
		return $this->varName;
	}

	/**
	 * Applies validation rules to a request variable
	 *
	 * @throws CheckException when validation fails
	 */
	public function validate()
	{
		foreach ($this->checkList as $check)
		{
			if (!$check->isValid($this->request->get($this->varName)))
			{
				throw new CheckException($check->getViolationMsg());
			}
		}
	}

	/**
	 * Applies all registered filters sequentialy to a value
	 *
	 */
	public function filter()
	{
		foreach ($this->filterList as $filter)
		{
			$this->request->set($this->varName,
									 $filter->apply($this->request->get($this->varName)));
		}
	}

	public function getCheckData()
	{
		$data = array();
		foreach ($this->checkList as $check)
		{
			$name = get_class($check);
			$constraintList = $check->getParamList();
			$errMsg = $check->getViolationMsg();
			$data[$name] = array("error" => $errMsg,
								 "param" => $constraintList);
		}
		return $data;
	}

	public function getFilterData()
	{
		$data = array();
		foreach ($this->filterList as $filter)
		{
			$name = get_class($filter);
			$data[$name] = $filter->getParamList();
		}
		return $data;
	}

	public function getChecks()
	{
		return $this->checkList;
	}

	public function getFilters()
	{
		return $this->filterList;
	}
}

?>
