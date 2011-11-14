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
abstract class CreditCardPayment extends OnlinePayment
{
	/**
	 *	Credit card number (without spaces)
	 */
	protected $cardNumber;

	/**
	 *	Credit card expiration month
	 */
	protected $expiryMonth;

	/**
	 *	Credit card expiration year
	 */
	protected $expiryYear;

	/**
	 *	CVV2, CVC2 or CID code
	 */
	protected $cardCode;

	/**
	 *	Credit card type (Visa, MasterCard, etc.)
	 */
	protected $cardType;

	public function setCardData($cardNumber, $expiryMonth, $expiryYear, $cardCode = null)
	{
		$cardNumber = str_replace(' ', '', $cardNumber);
		$this->cardNumber = $cardNumber;
		$this->expiryMonth = $expiryMonth;
		$this->expiryYear = $expiryYear;
		$this->cardCode = $cardCode;
	}

	public function setCardType($type)
	{
		$this->cardType = $type;
	}

	public function getCardNumber()
	{
		return $this->cardNumber;
	}

	public function getExpirationMonth()
	{
		return $this->expiryMonth;
	}

	public function getExpirationYear()
	{
		return $this->expiryYear;
	}

	public function getCardCode()
	{
		return $this->cardCode;
	}

	public function getCardType()
	{
		return $this->cardType;
	}

	public function toArray()
	{
		return array('type' => 'CC');
	}

	public function isCardNumberStored()
	{
		return false;
	}

	public function isCvvRequired()
	{
		return true;
	}

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

	/**
	 *	Determines if credit card type needs to be passed to payment processor
	 */
	abstract public function isCardTypeNeeded();
}

?>
