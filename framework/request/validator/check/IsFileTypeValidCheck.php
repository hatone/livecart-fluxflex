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
 * Checks if a file with the correct extension has been uploaded
 *
 * @package framework.request.validator.check
 * @author Integry Systems
 */
class IsFileTypeValidCheck extends Check
{
	private $extensions;

	public function __construct($violationMsg, $extensions)
	{
		parent::__construct($violationMsg);
		$this->setParam('extensions', $extensions);
	}

	public function isValid($value)
	{
		if (!empty($value) || empty($value['name']))
		{
			return true;
		}

		$ext = trim(strtolower(pathinfo($value['name'], PATHINFO_EXTENSION)));
		return in_array($ext, $this->getParam('extensions'));
	}
}

?>
