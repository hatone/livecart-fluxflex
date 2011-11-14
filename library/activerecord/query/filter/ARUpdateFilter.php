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

require_once("filter/ARFilter.php");

/**
 *
 * @package activerecord.query.filter
 * @author Integry Systems
 */
class ARUpdateFilter extends ARFilter
{
	/**
	 * List of modifiers
	 *
	 * @var unknown_type
	 */
	private $modifierList = array();

	public function createString()
	{
		$result = "";
		if (!empty($this->modifierList))
		{
			$result .= " SET ";
			$statementList = array();
			foreach($this->modifierList as $fieldName => $newValue)
			{
				if ($newValue instanceof ARExpressionHandle)
				{
					$value = $newValue->toString();
				}
				else
				{
					$value = "'".$newValue."'";
				}
				$statementList[] = $fieldName . " = ". $value;
			}
			$result .= implode(", ", $statementList);
		}
		$result .= parent::createString();
		return $result;
	}

	/**
	 * Sets a field modifier (value which will be assigned for a row(s) matching
	 * update condition)
	 *
	 * @param string $fieldName
	 * @param string $fieldValue
	 *
	 * @todo add ARFieldHandle (field object) instead of $fieldName (string)
	 */
	public function addModifier($fieldName, $fieldValue)
	{
		$this->modifierList[$fieldName] = $fieldValue;
	}

	public function isModifierSet()
	{
		return count($this->modifierList) > 0;
	}
}

?>
