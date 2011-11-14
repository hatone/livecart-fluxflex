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
 *	Abstracts SQLs CASE statement
 *	http://dev.mysql.com/doc/refman/5.0/en/control-flow-functions.html
 *
 *  @package activerecord.query.filter
 *  @author Integry Systems
 */
class ARCaseHandle implements ARFieldHandleInterface
{
  	protected $conditions = array();

  	protected $defaultValue;

	public function __construct(ARFieldHandleInterface $defaultValue = null)
  	{
		if (!is_null($defaultValue))
		{
			$this->defaultValue = $defaultValue;
		}
	}

	public function addCondition(Condition $condition, ARFieldHandleInterface $value)
	{
		$this->conditions[] = array($condition, $value);
	}

	public function prepareValue($value)
	{
		return $value;
	}

	public function escapeValue($value)
	{
		return $value;
	}

	public function toString()
	{
	  	$cases = array('CASE');

		foreach ($this->conditions as $cond)
	  	{
			$cases[] = 'WHEN ' . $cond[0]->createChain() . ' THEN ' . $cond[1]->toString();
		}

		if ($this->defaultValue)
		{
			$cases[] = 'ELSE ' . $this->defaultValue->toString();
		}

		$cases[] = 'END';

		return implode(' ', $cases);
	}
}

?>
