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
 * Implements methods of CustomerOrder
 *
 * @author Integry Systems
 * @package application.model.businessrule
 */
interface BusinessRuleOrderInterface
{
	public function getPurchasedItems();
	public function getCompletionDate();
	public function getTotal();
	public function getCurrency();
}

?>
