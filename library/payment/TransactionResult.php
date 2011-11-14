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

include_once dirname(__FILE__) . '/TransactionValueMapper.php';

/**
 *
 * @package library.payment
 * @author Integry Systems
 */
class TransactionResult
{
	public $gatewayTransactionID;

	public $amount;

	public $currency;

	public $AVSaddr;

	public $AVSzip;

	public $CVVmatch;

	public $details;

	public $rawResponse;

	protected $isCaptured;

	protected $type;

	const TYPE_SALE = 0;
	const TYPE_AUTH = 1;
	const TYPE_CAPTURE = 2;
	const TYPE_VOID = 3;
	const TYPE_REFUND = 4;

	public function __construct()
	{
		$this->gatewayTransactionID = new TransactionValueMapper();
		$this->amount = new TransactionValueMapper();
		$this->currency = new TransactionValueMapper();
		$this->AVSaddr = new TransactionValueMapper();
		$this->AVSzip = new TransactionValueMapper();
		$this->CVVmatch = new TransactionValueMapper();
		$this->details = new TransactionValueMapper();
		$this->rawResponse = new TransactionValueMapper();
	}

	public function setTransactionType($type)
	{
		$this->type = $type;
	}

	public function getTransactionType()
	{
		return $this->type;
	}

	public function isCaptured()
	{
		return (self::TYPE_SALE == $this->type) || (self::TYPE_CAPTURE == $this->type);
	}

	public function getDetails()
	{
		return $this->rawResponse;
	}
}

?>
