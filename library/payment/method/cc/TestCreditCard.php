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

include_once(dirname(__file__) . '/../../abstract/CreditCardPayment.php');

/**
 *
 * @package library.payment.method.cc
 * @author Integry Systems
 */
class TestCreditCard extends CreditCardPayment
{
	public function isCreditable()
	{
		return true;
	}

	public function isCardTypeNeeded()
	{
		return false;
	}

	public function isVoidable()
	{
		return true;
	}

	public function isMultiCapture()
	{
		return true;
	}

	public function isCapturedVoidable()
	{
		return true;
	}

	/**
	 *	All currencies supported, except LTL
	 */
	public function getValidCurrency($currentCurrencyCode)
	{
		return 'LTL' != $currentCurrencyCode ? $currentCurrencyCode : 'USD';
	}

	/**
	 *	Reserve funds on customers credit card
	 */
	public function authorize()
	{
		$result = $this->process('');
		if ($result instanceof TransactionResult)
		{
			$result->setTransactionType(TransactionResult::TYPE_AUTH);
		}

		return $result;
	}

	/**
	 *	Capture reserved funds
	 */
	public function capture()
	{
		$result = $this->process('Capture');
		if ($result instanceof TransactionResult)
		{
			$result->setTransactionType(TransactionResult::TYPE_CAPTURE);
		}

		return $result;
	}

	/**
	 *	Credit (a part) of customers payment
	 */
	public function credit()
	{
		$result = $this->process('');
		if ($result instanceof TransactionResult)
		{
			$result->setTransactionType(TransactionResult::TYPE_VOID);
		}

		return $result;
	}

	/**
	 *	Void the payment (issue full credit)
	 */
	public function void()
	{
		$result = $this->process('Void');
		if ($result instanceof TransactionResult)
		{
			$result->setTransactionType(TransactionResult::TYPE_VOID);
		}
		return $result;
	}

	/**
	 *	Authorize and capture funds within one transaction
	 */
	public function authorizeAndCapture()
	{
		$result = $this->process('Sale');
		if ($result instanceof TransactionResult)
		{
			$result->setTransactionType(TransactionResult::TYPE_SALE);
		}

		return $result;
	}

	public function toArray()
	{
		$ret = parent::toArray();
		$ret['name'] = 'Test';
		return $ret;
	}

	private function process($type)
	{
		if ($this->getCardCode() != '000' && 'Void' != $type && 'Capture' != $type)
		{
			return new TransactionError($this->details, '');
		}

		$result = new TransactionResult();
		$result->gatewayTransactionID->set('TESTCC' . rand(1, 10000000));
		$result->amount->set($this->details->amount->get());
		$result->currency->set($this->details->currency->get());

		$result->AVSaddr->set(true);
		$result->AVSzip->set(true);
		$result->CVVmatch->set(true);

		return $result;
	}
}

?>
