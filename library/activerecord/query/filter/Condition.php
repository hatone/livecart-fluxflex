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
 * Abstract condition element used in WHERE clause
 *
 * It allows programmer to create schema independent conditional statems and use it
 * with a filter (ARFilter subclass) that is applied to a record set (deletion,
 * select or etc.)
 *
 * @package activerecord.query.filter
 * @author Integry Systems
 */
abstract class Condition
{
	/**
	 * Operator string
	 *
	 * @var unknown_type
	 */
	protected $operatorString = "";
	protected $ORCondList = array();
	protected $ANDCondList = array();

	abstract public function toString();
	abstract public function toPreparedStatement();

	public function createChain()
	{
		$condStr = "(" . $this->toString();
		foreach($this->ANDCondList as $andCond)
		{
			$condStr .= " AND " . $andCond->createChain();
		}
		foreach($this->ORCondList as $orCond)
		{
			$condStr .= " OR " . $orCond->createChain();
		}
		$condStr .= ")";
		return $condStr;
	}

	public function createPreparedChain()
	{
		$values = array();

		$sql = $this->toPreparedStatement();
		$condStr = "(" . $sql['sql'];
		$values = array_merge($values, $sql['values']);

		foreach($this->ANDCondList as $andCond)
		{
			$sql = $andCond->createPreparedChain();
			$values = array_merge($values, $sql['values']);
			$condStr .= " AND " . $sql['sql'];
		}

		foreach($this->ORCondList as $orCond)
		{
			$sql = $orCond->createPreparedChain();
			$values = array_merge($values, $sql['values']);
			$condStr .= " OR " . $sql['sql'];
		}

		$condStr .= ")";

		return array('sql' => $condStr, 'values' => $values);
	}

	public function setOperatorString($str)
	{
		$this->operatorString = $str;
	}

	public function getOperatorString()
	{
		return $this->operatorString;
	}

	/**
	 * Appends a condition with a strict (AND) requirement
	 *
	 * @param Condition $cond
	 */
	public function addAND(Condition $cond)
	{
		$this->ANDCondList[] = $cond;
	}

	/**
	 *
	 *
	 * @param Condition $cond
	 */
	public function addOR(Condition $cond)
	{
		$this->ORCondList[] = $cond;
	}

	public function getANDCondList()
	{
		return $this->ANDCondList;
	}

	public function getORCondList()
	{
		return $this->ORCondList;
	}

	/**
	 * Creates an expression handle, which can be used in query field list for advanced calculations
	 *
	 * For example:
	 * <code>
	 *		$cond = new EqualsOrMoreCond(new ARFieldHandle('ProductPrice', 'price'), 20);
	 *		$filter->addField('SUM(' . $cond->getExpressionHandle() . ')');
	 * </code>
	 *
	 * @param array $array Array of Condition object instances
	 * @return Condition
	 */
	public function getExpressionHandle()
	{
		return new ARExpressionHandle($this->createChain());
	}

	public function removeCondition(BinaryCondition $condition)
	{
		$str = $condition->toString();

		if ($this->toString() == $str)
		{
			$this->leftSide = new ARExpressionHandle(1);
			$this->rightSide = 1;
			$this->operatorString = '=';
		}

		foreach (($this->ANDCondList + $this->ORCondList) as $cond)
		{
			$cond->removeCondition($condition);
		}
	}

	/**
	 * Merges an array of conditions into one condition
	 *
	 * @param array $array Array of Condition object instances
	 * @return Condition
	 */
	public static function mergeFromArray($array, $or = false)
	{
		if (!$array)
		{
			return null;
		}

		$baseCond = array_shift($array);
		foreach ($array as $cond)
		{
			if (!$or)
			{
				$baseCond->addAND($cond);
			}
			else
			{
				$baseCond->addOR($cond);
			}
		}

		return $baseCond;
	}

	public function __clone()
	{
		foreach (array('ORCondList', 'ANDCondList') as $var)
		{
			$vars =& $this->$var;
			if (is_array($vars))
			{
				foreach ($vars as $index => $cond)
				{
					$vars[$index] = clone $cond;
				}
			}
		}
	}

	public function __destruct()
	{
//		logDestruct($this, $this->createChain());
	}
}

/**
 * Base class for unary conditions (using unary operators, such as NULL)
 *
 * @package activerecord.query.filter
 */
class UnaryCondition extends Condition
{
	protected $fieldHandle = null;

	public function __construct(ARFieldHandleInterface $fieldHandle, $operatorString)
	{
		$this->fieldHandle = $fieldHandle;
		$this->operatorString = $operatorString;
	}

	public function toString()
	{
		return $this->fieldHandle->toString().' '.$this->operatorString;
	}

	public function toPreparedStatement()
	{
		return array('sql' => $this->toString(), 'values' => array());
	}
}

/**
 * Base class for binary conditions (using binary operators, such as =, =<, =>, > and etc.)
 *
 * @package activerecord.query.filter
 */
abstract class BinaryCondition extends Condition
{
	protected $leftSide = "";
	protected $rightSide = "";

	protected $leftSideTableName = "";
	protected $rightSideTableName = "";

	public function __construct(ARFieldHandleInterface $leftSide, $rightSide)
	{
		$this->leftSide = $leftSide;
		$this->rightSide = $rightSide;
	}

	public function toPreparedStatement()
	{
		$condStr = $this->leftSide->toString() . $this->operatorString;
		$values = array();

		if ($this->rightSide instanceof ARFieldHandleInterface)
		{
			$condStr .= $this->rightSide->toString();
		}
		else
		{
			$field = $this->leftSide->getField();
			if ($field)
			{
				$field = $field->getDataType();
			}

			if ($field instanceof ARFloat)
			{
				$type = 'float';
			}
			elseif ($field instanceof ARNumeric)
			{
				$type = 'int';
			}
			else if ($field instanceof ARPeriod)
			{
				$type = 'timestamp';
			}
			else
			{
				$type = 'string';
			}

			$value = $this->leftSide->prepareValue($this->rightSide);
			$id = md5($value);
			$values[$id] = array('value' => $value,
								 'type' => $type);

			$condStr .= '???' . $id . '@@@';
		}

		return array('sql' => $condStr, 'values' => $values);
	}

	public function toString()
	{
		$condStr = $this->leftSide->toString() . $this->operatorString;
		if ($this->rightSide instanceof ARFieldHandleInterface)
		{
			$condStr .= $this->rightSide->toString();
		}
		else
		{
			$condStr .= $this->leftSide->escapeValue($this->rightSide);
		}
		return $condStr;
	}

	public function getLeftSide()
	{
		return $this->leftSide;
	}

	public function getRightSide()
	{
		return $this->rightSide;
	}
}

/**
 * Binary condition with operator
 * <code>
 * new OperatorCond("User.creatimDate", "2005-02-02", ">");
 * </code>
 *
 * @package activerecord.query.filter
 */
class OperatorCond extends BinaryCondition
{
	protected $operatorString;

	public function __construct($leftSide, $rightSide, $operator)
	{
		parent::__construct($leftSide, $rightSide);
		$this->operatorString = $operator;
	}
}

/**
 * Equals condition
 *
 * @package activerecord.query.filter
 */
class EqualsCond extends BinaryCondition
{
	protected $operatorString = "=";
}

/**
 * Not equals condition
 *
 * @package activerecord.query.filter
 */
class NotEqualsCond extends BinaryCondition
{
	protected $operatorString = "!=";
}

/**
 * Is NULL condition
 *
 * @package activerecord.query.filter
 */
class IsNullCond extends UnaryCondition
{
	public function __construct(ARFieldHandleInterface $fieldHandle)
	{
		parent::__construct($fieldHandle, "IS NULL");
	}
}
/**
 * IS NOT NULL condition
 *
 * @package activerecord.query.filter
 */
class IsNotNullCond extends UnaryCondition
{
	public function __construct(ARFieldHandleInterface $fieldHandle)
	{
		parent::__construct($fieldHandle, "IS NOT NULL");
	}
}

/**
 * Less than condition
 *
 * @package activerecord.query.filter
 */
class LessThanCond extends BinaryCondition
{
	protected $operatorString = "<";
}

/**
 * Condition using ">" operator
 *
 * @package activerecord.query.filter
 */
class MoreThanCond extends BinaryCondition
{
	protected $operatorString = ">";
}

/**
 * Condition using LIKE operator
 *
 * @package activerecord.query.filter
 */
class LikeCond extends BinaryCondition
{
	protected $operatorString = " LIKE ";
}

/**
 * Condition using REGEXP operator
 *
 * @package activerecord.query.filter
 */
class RegexpCond extends BinaryCondition
{
	protected $operatorString = " REGEXP ";
}

/**
 * Condition using IS operator
 *
 * @package activerecord.query.filter
 */
class ISCond extends BinaryCondition
{
	protected $operatorString = " IS ";
}

/**
 * Condition using IN operator
 *
 * @package activerecord.query.filter
 */
class INCond extends BinaryCondition
{
	protected $operatorString = " IN ";

	public function __construct(ARFieldHandleInterface $leftSide, $rightSide)
	{
		if (is_array($rightSide))
		{
		  	$rightSide = implode(', ', array_filter($rightSide, array($this, 'filterEmptyValues')));
		}

		if (!$rightSide)
		{
			$rightSide = 0;
		}

		parent::__construct($leftSide, "(".$rightSide.")");
	}

	public function toPreparedStatement()
	{
		return array('sql' => $this->toString(), 'values' => array());
	}

	private function filterEmptyValues($value)
	{
		return is_numeric($value) || trim($value);
	}
}

/**
 * Condition using NOT IN operator
 *
 * @package activerecord.query.filter
 */
class NotINCond extends INCond
{
	protected $operatorString = " NOT IN ";
}

/**
 * Condition using "=>" operator
 *
 * @package activerecord.query.filter
 */
class EqualsOrMoreCond extends BinaryCondition
{
	protected $operatorString = ">=";
}

/**
 * Condition using "=<" operator
 *
 * @package activerecord.query.filter
 */
class EqualsOrLessCond extends BinaryCondition
{
	protected $operatorString = "<=";
}

class AndChainCondition extends Condition
{
	public function __construct($array = array())
	{
		foreach ($array as $cond)
		{
			$this->addAND($cond);
		}
	}

	public function getOperator()
	{
		return ' AND ';
	}

	public function toString()
	{

	}

	public function toPreparedStatement()
	{
		return array('sql' => '', 'values' => array());
	}

	public function createChain()
	{
		$conds = array();
		foreach ($this->ANDCondList as $cond)
		{
			$conds[] = $cond->createChain();
		}

		return '(' . implode($this->getOperator(), $conds) . ')';
	}

	public function createPreparedChain()
	{
		$values = array();

		$conds = array();
		foreach($this->ANDCondList as $andCond)
		{
			$sql = $andCond->createPreparedChain();
			$values = array_merge($values, $sql['values']);
			$conds[] = $sql['sql'];
		}

		return array('sql' => '(' . implode($this->getOperator(), $conds) . ')', 'values' => $values);
	}
}

class OrChainCondition extends AndChainCondition
{
	public function getOperator()
	{
		return ' OR ';
	}
}

?>
