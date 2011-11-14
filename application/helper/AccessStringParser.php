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
 * @package application.helper
 * @author Integry Systems
 */
class AccessStringParser
{
	public static function run($accessString)
	{
		if(empty($accessString)) return true;
		
		if(preg_match_all('/([\w\.]+)(?:\(([\w\.]*)(?:\/(\w*))?\))?,?/', $accessString, $roles))
		{		
			ClassLoader::import('application.model.user.SessionUser');
			
			$currentUser = SessionUser::getUser();
			$controller = Controller::getCurrentController();
			$rolesParser = $controller->getRoles();
			
			$currentControllerName = $controller->getRequest()->getControllerName();
			$currentActionName = $controller->getRequest()->getActionName();

			$rolesCount = count($roles[0]);
			for($i = 0; $i < $rolesCount; $i++)
			{
				$roleString = $roles[0][$i];
				$roleName = $roles[1][$i];
				$roleControllerName = empty($roles[3][$i]) ? $currentControllerName : $roles[2][$i];
				$roleActionName = empty($roles[3][$i]) ? (empty($roles[2][$i]) ? $currentActionName : $roles[2][$i]) : $currentActionName;
				
				if($roleControllerName == $currentControllerName && $roleActionName == $currentActionName)
				{
					$aRoleName = $rolesParser->getRole($roleActionName);
		   			if($currentUser->hasAccess($aRoleName) && $currentUser->hasAccess($roleName))
					{
						return true;
					}
				}
			}
			
			return false;
		}
		
		throw new ApplicationException('Access string ("'. $accessString .'") has illegal format');
	}
}
?>
