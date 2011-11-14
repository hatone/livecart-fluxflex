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

include_once(dirname(__file__) . '/ARFieldHandleInterface.php');

/**
 *
 * @package activerecord.query.filter
 * @author Integry Systems
 */
class ARExpressionHandle implements ARFieldHandleInterface
{
  	protected $expression;

	public function __construct($expression)
  	{
		$this->expression = $expression;
	}

	public function prepareValue($value)
	{
		return $value;
	}

	public function escapeValue($value)
	{
		return is_numeric($value) ? $value : '0x' . bin2hex($value);
	}

	public function toString()
	{
	  	return $this->expression;
	}

	public function getField()
	{
		return null;
	}

	public function __toString()
	{
	  	return $this->toString();
	}
}

?>
