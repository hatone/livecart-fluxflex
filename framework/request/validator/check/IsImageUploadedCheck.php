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

ClassLoader::import("framework.request.validator.check.Check");

/**
 * Checks if a file has been uploaded
 *
 * @package framework.request.validator.check
 * @author Integry Systems
 */
class IsImageUploadedCheck extends Check
{
	public function isValid($value)
	{
		$field = $this->getParam('fieldName');
		if (!isset($_FILES[$field]) || empty($_FILES[$field]['tmp_name']))
		{
			return false;
		}
		else
		{
		  	$i = getimagesize($_FILES[$field]['tmp_name']);

			// not a valid image
			if (!$i)
			{
			  	return false;
			}

			// check image type
			$type = $i[2];

			$validTypes = $this->getParam('imageTypes');

			if (is_array($validTypes))
			{
				return isset($validTypes[$type]);
			}
			else
			{
				return true;
			}
		}
	}

	/**
	 *  Define valid image types
	 *
	 *  @param $typeArray Array Valid image types (extensions). Ex: array('GIF', 'JPEG', 'PNG') or array (1, 2, 3)
	 */
	public function setValidTypes($typeArray)
	{
		$types = array();
		foreach ($typeArray as $ext)
		{
			if (is_int($ext))
			{
				$types[$ext] = true;
			}
			else
			{
				if (defined('IMAGETYPE_' . $ext))
				{
				  	$types[constant('IMAGETYPE_' . $ext)] = true;
				}
			}
		}

	  	$this->setParam('imageTypes', $types);
	}

	public function setFieldName($fieldName)
	{
	  	$this->setParam('fieldName', $fieldName);
	}
}

?>
