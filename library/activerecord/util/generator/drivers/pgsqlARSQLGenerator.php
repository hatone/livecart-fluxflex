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
 * ...
 * 
 * @package activerecord.util.generator.drivers
 * @author Integry Systems
 */
class pgsqlARSQLGenerator extends ARSQLGenerator {
		
	protected function _defineField(ARField $field, $autoincrement = true, $not_null = true) {
		
		$sql = '';		
		$type = $field->getDataType();
		$length = $type->getLength();
		
		if (!empty($length)) {
		  	
		 	$length = "(".$length.") " ;
		} else {
		  
		  	$length = " ";
		}		
		
		if ($autoincrement) {
		  
		  	if ($field instanceof ARPrimaryKeyField) {
				
				return $sql."serial NOT NULL ";
			}
		}
						
		switch (get_class($type)) {
		
			case 'Integer':
			
				$sql .= "numeric".$length;	
			break;			
			case 'Bool':
			
				$sql .= "numeric(1) ";
			break;				
			case 'Binary':
			
				$sql .= "bit".$length;
			break;
			case 'DateTime':
			
				$sql .= "timestamp ";
			break;
			case 'Float':
				
				$sql .= "float ";
			break;					
			default:  //integer, varchar, char, time, datetime, date

				$sql .= get_class($type).$length;
			break;
		}		
		
		if ($not_null) {
		
			$sql .= "NOT NULL ";		
		}
		return $sql;
	}
	
	protected function _modifyStatement($field) {
		
		return " DROP ".$field->getName().", ADD  ".$field->getName()." ";
	}
	
	protected function _sqlFromColumn(ColumnInfo $column, $auto_increment = true) {
	  
	  	if ($column->nativeType == 'float8') {
			
			return 'float not null ';
		} else {
		  
		  	return parent::_sqlFromColumn($column, $auto_increment);
		}  		
	}
	
	
	
}

?>
