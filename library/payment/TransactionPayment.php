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

include_once('PaymentException.php');
include_once('TransactionResult.php');
include_once('TransactionError.php');
include_once('TransactionDetails.php');

/**
 *
 * @package library.payment
 * @author Integry Systems
 */
abstract class TransactionPayment
{
	protected $details;

	protected $isTestTransaction = false;

	protected $application;

	protected $order;

	private $config = array();

	public function __construct(TransactionDetails $transactionDetails = null)
	{
		$this->details = $transactionDetails;
	}

	public function setAsTestTransaction($test = true)
	{
		$this->isTestTransaction = true;
	}

	public function setConfigValue($key, $value)
	{
		$this->config[$key] = $value;
	}

	public function getConfigValue($key, $defaultValue = '')
	{
		if (isset($this->config[$key]))
		{
			return $this->config[$key];
		}
		else
		{
			return $defaultValue;
		}
	}

	public function getDetails()
	{
		return $this->details;
	}

	public function setDetails(TransactionDetails $transactionDetails)
	{
		$this->details = $transactionDetails;
	}

	public function setApplication(LiveCart $application)
	{
		$this->application = $application;
	}

	public function setOrder(CustomerOrder $order)
	{
		$this->order = $order;
		$this->order->setPaymentMethod(get_class($this));
	}

	/**
	 *	Determine if the payment method supports crediting a refund payment back to customer
	 */
	public abstract function isVoidable();

	public abstract function void();

	/**
	 *	Return a valid currency code if the supplied currency is not supported by this payment method
	 */
	public abstract function getValidCurrency($currentCurrencyCode);
}

?>
