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
 *
 * @package application.model.system
 * @author Integry Systems <http://integry.com>
 */
class ActiveRecordGroup
{
	public static function mergeGroupsWithFields($className, $groups, $fields)
	{
		$fieldsWithGroups = array();
		$k = $i = 1;
		$fieldsCount = count($fields);
		foreach($fields as $field)
		{
			if(isset($field[$className]) && !empty($field[$className]['ID']))
			{
				$shiftGroup = false;
				while($group = array_shift($groups))
				{
					if($group['position'] < $field[$className]['position'])
					{
						$shiftGroup = true;
						$fieldsWithGroups[$i++] = array($className => $group);
					}
					else
					{
						if($group['position'] > $field[$className]['position'])
						{
							array_unshift($groups, $group);
						}
						break;
					}
		 		}
			}

			$fieldsWithGroups[$i++] = $field;
			$k++;
	   }

	   while($group = array_shift($groups))
	   {
		   $fieldsWithGroups[$i++] = array($className => $group);
	   }

	   return $fieldsWithGroups;
	}
}

?>
