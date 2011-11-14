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

include_once dirname(__file__) . '/MassActionProcessor.php';

/**
 * @package application.helper.massAction
 * @author Integry Systems
 */
class UserMassActionProcessor extends MassActionProcessor
{
	protected function processRecord(User $user)
	{
		if (substr($this->getAction(), 0, 7) == 'enable_')
		{
			$user->setFieldValue('isEnabled', 1);
		}
		else if (substr($this->getAction(), 0, 8) == 'disable_')
		{
			$user->setFieldValue('isEnabled', 0);
		}
	}
}

?>
