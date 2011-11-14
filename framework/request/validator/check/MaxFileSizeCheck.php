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
 * Checks if a file size does not exceed predefined max size
 *
 * @package framework.request.validator.check
 * @author Integry Systems
 */
class MaxFileSizeCheck extends Check
{
	private $extensions;

	public function __construct($violationMsg, $maxSize)
	{
		parent::__construct($violationMsg);
		$this->setParam('maxSize', $maxSize);
	}

	public function isValid($value)
	{
		$maxSize = $this->getParam('maxSize');
		if (!$maxSize)
		{
			return true;
		}

		return $value['size'] < ($maxSize * 1024 * 1024);
	}
}

?>
