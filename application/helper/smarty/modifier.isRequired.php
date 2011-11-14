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
 *  Determine if a check has been applied on a form field
 *
 *  @package application.helper.smarty
 *  @author Integry Systems 
 */
function smarty_modifier_isRequired(Form $form, $fieldName, $check = 'IsNotEmptyCheck')
{
	$checkData = $form->getValidator()->getValidatorVar($fieldName)->getCheckData();  
	
	return isset($checkData[$check]);	
}

?>
