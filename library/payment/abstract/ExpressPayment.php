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

include_once(dirname(__file__) . '/OnlinePayment.php');

/**
 *
 * @package library.payment.abstract
 * @author Integry Systems 
 */
abstract class ExpressPayment extends OnlinePayment
{
	abstract public function getInitUrl($returnUrl, $cancelUrl, $sale = true);
	
	/**
	 *	Reserve funds on customers credit card
	 */
	abstract public function authorize();
	
	/**
	 *	Capture reserved funds
	 */
	abstract public function capture();
	
	/**
	 *	Authorize and capture funds within one transaction
	 */
	abstract public function authorizeAndCapture();
}

?>
