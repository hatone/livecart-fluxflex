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

include_once("schema/ARField.php");
include_once("schema/ARSchemaDataType.php");

/**
 * Class for table structure representation (also known as schema)
 *
 * @package activerecord.schema
 * @author Integry Systems
 */
class ARSchema
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	private $tableName = "";

	private $fieldList = array();

	private $foreignKeyList = array();

	private $primaryKeyList = array();

	private $fieldsByType = array();

	private $arrayFieldList = null;

	/**
	 *	Cache array of referenced schema instances for faster lookups
	 */
	private $referencedSchemaList = null;

	private $directlyReferencedSchemaList = null;

	private $circularReferences = array();

	private $autoReferences = array();

	private $autoReferencedSchemas = array();

	private $cachedRecursiveReferences = null;

	private $skippedReferences = array();

	public function registerField(ARField $schemaField, $reference = true)
	{
		$name = $schemaField->getName();
		$this->fieldList[$name] = $schemaField;

		if (($schemaField instanceof ARForeignKey))
		{
			$this->foreignKeyList[$name] = $schemaField;
		}

		if ($schemaField instanceof ARPrimaryKey)
		{
			$this->primaryKeyList[$name] = $schemaField;
		}

		if (!$reference)
		{
			$this->skippedReferences[$name] = true;
		}

		$this->fieldsByType[get_class($schemaField->getDataType())][$name] = $schemaField;
	}

	public function unregisterField($name)
	{
		if (isset($this->fieldList[$name]))
		{
			unset($this->fieldsByType[get_class($this->fieldList[$name]->getDataType())][$name]);
			unset($this->foreignKeyList[$name]);
			unset($this->primaryKeyList[$name]);
			unset($this->skippedReferences[$name]);
			unset($this->fieldList[$name]);
		}
	}

	/**
	 * Checks if a given field exists in schema
	 *
	 * @param sting $fieldName
	 * @return bool
	 */
	public function fieldExists($fieldName)
	{
		return !empty($this->fieldList[$fieldName]);
	}

	/**
	 * Gets a table name which is represented by this schema
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->tableName;
	}

	/**
	 * Sets a table name
	 *
	 * @param string $tableName
	 */
	public function setName($tableName)
	{
		$this->tableName = $tableName;
	}

	/**
	 * Gets a schema field name
	 *
	 * @param string $name
	 * @return ARSchemaField Schema field instance
	 */
	public function getField($name)
	{
		if (isset($this->fieldList[$name]))
		{
			return $this->fieldList[$name];
		}
	}

	public function getHandle($field)
	{
		return new ARFieldHandle($this->getName(), $field);
	}

	/**
	 * Gets a list of all schema fields
	 *
	 * @param int $fieldClass
	 * @return ARSchemaField[]
	 */
	public function getFieldList()
	{
		return $this->fieldList;
	}

	/**
	 * Gets a list of foreign keys defined for this schema
	 *
	 * @return ARPrimaryKey[]
	 */
	public function getForeignKeyList()
	{
		return $this->foreignKeyList;
	}

	public function getReferencedForeignKeyList()
	{
		return array_diff_key($this->foreignKeyList, $this->skippedReferences);
	}

	/**
	 * Gets a list of primary key fields defined for this schema
	 *
	 * @return ARForeignKey[]
	 */
	public function getPrimaryKeyList()
	{
		return $this->primaryKeyList;
	}

	/**
	 * Returns a list of ARArray schema fields
	 *
	 * @return ARField[]
	 */
	public function getFieldsByType($className)
	{
		return isset($this->fieldsByType[$className]) ? $this->fieldsByType[$className] : array();
	}

	/**
	 * Returns a list of ARArray schema field names
	 *
	 * @return ARField[]
	 * @todo remove
	 */
	public function getArrayFieldList()
	{
		if (is_null($this->arrayFieldList))
		{
			$this->arrayFieldList = array_keys($this->getFieldsByType('ARArray'));
		}

		return $this->arrayFieldList;
	}

	/**
	 * Creatres a sting of enumerated fields
	 *
	 * @param string $prefix Prefix to add for each field (field renaming)
	 * @param string $separator Field separator
	 */
	public function enumerateFields($prefix = "", $separator = ",")
	{
		$fieldList = array();
		foreach($this->fieldList as $name => $field)
		{
			$fieldStr = $this->tableName . '.' . $name;
			if (!empty($prefix))
			{
				$fieldStr .= ' AS ' . $prefix.$name;
			}
			$fieldList[] = $fieldStr;
		}
		return implode($separator, $fieldList);
	}

	/**
	 * Returns an array of all directly and indirectly referenced schemas (infinite levels of referencing)
	 *
	 * @todo Think of a better way to handle circular references
	 */
	public function getReferencedSchemas($circularReference = false)
	{
		if (is_null($this->referencedSchemaList))
		{
			$ret = array();

			foreach($this->getForeignKeyList() as $name => $refField)
			{
				$refSchema = ActiveRecord::getSchemaInstance($refField->getForeignClassName());

				if (($this === $refSchema) || ($refSchema === $circularReference) || isset($this->skippedReferences[$name]))
				{
					continue;
				}

				$refName = $refField->getReferenceName();
				if(!isset($ret[$refName]))
				{
					$ret[$refName] = array();
				}

				$ret[$refName][] = $refSchema;
				$this->directlyReferencedSchemaList[$refName][] = $refSchema;

				$sub = $refSchema->getReferencedSchemas($this);
				$ret = array_merge($ret, $sub);

				// remove circular references
				unset($ret[$this->tableName]);
			}

			$this->referencedSchemaList = $ret;
		}

		if ($this->circularReferences)
		{
			$ret = $this->referencedSchemaList;
			foreach ($this->circularReferences as $refName => $refSchema)
			{
				$ret[$refName][] = $refSchema;
				$sub = $refSchema->getReferencedSchemas();
				$ret = array_merge($ret, $sub);
			}

			$this->referencedSchemaList = $ret;
			$this->circularReferences = array();
		}

		return $this->referencedSchemaList;
	}

	public function getDirectlyReferencedSchemas()
	{
		$this->getReferencedSchemas();
		return $this->directlyReferencedSchemaList;
	}

	public function registerCircularReference($refName, $refSchema)
	{
		$refSchema = ActiveRecord::getSchemaInstance($refSchema);
		$refSchema->getReferencedSchemas($this);
		$this->circularReferences[$refName] = $refSchema;
	}

	public function registerAutoReference($fieldName)
	{
		$foreignClassName = $this->getField($fieldName)->getForeignClassName();
		$this->autoReferences[] = $foreignClassName;
		$this->autoReferencedSchemas[] = ActiveRecord::getSchemaInstance($foreignClassName);
	}

	public function getAutoReferences()
	{
		return $this->autoReferences;
	}

	public function getRecursiveAutoReferences()
	{
		if (is_null($this->cachedRecursiveReferences))
		{
			$autoReferences = $this->autoReferences;
			foreach ($this->autoReferencedSchemas as $schema)
			{
				$autoReferences = array_merge($autoReferences, $schema->getAutoReferences());
			}

			$this->cachedRecursiveReferences = $autoReferences;
		}

		return $this->cachedRecursiveReferences;
	}

	/**
	 * Checks if this schema is a valid one.
	 * Valid schema must have a name assigned
	 */
	public function isValid()
	{
		return !empty($this->tableName);
	}

	public function toArray()
	{
		return array_keys($this->fieldList);
	}
}

?>
