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

include_once('PaymentTest.php');

ClassLoader::import('library.payment.method.cc.EWayCvn');

/**
 *
 * @package library.payment.test
 * @author Integry Systems
 */
class EwayCvnTest extends PaymentTest
{
	private function getPaymentHandler()
	{
		$payment = new EWayCvn($this->details);
		$payment->setConfigValue('customerID', '87654321');
		$payment->setConfigValue('test', true);

		$payment->setCardData('4444333322221111', '12', '2010', '000');
		$payment->setCardType('Visa');

		return $payment;
	}

	function testInvalidCard()
	{
		$payment = $this->getPaymentHandler();
		$payment->setCardData('5522219712684510', '12', '2010', '000');
		$payment->setCardType('Visa');

		$result = $payment->authorizeAndCapture();

		$this->assertTrue($result instanceof TransactionError);
	}

	function testAuthorizeAndCapture()
	{
		$this->details->amount->set('321.17');
		$payment = $this->getPaymentHandler();

		$result = $payment->authorizeAndCapture();

		$this->assertTrue($result instanceof TransactionResult);
	}

	function testVoidAuthorizedTransaction()
	{
		$payment = $this->getPaymentHandler();

		$result = $payment->authorize();
		$this->details->gatewayTransactionID->set($result->gatewayTransactionID->get());

		$void = $this->getPaymentHandler();
		$result = $void->void();

		$this->assertTrue($result instanceof TransactionResult);
	}
}

?>
