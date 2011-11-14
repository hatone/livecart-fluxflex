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

include_once('ShippingResultInterface.php');

/**
 *
 * @package library.shipping
 * @author Integry Systems
 */
class ShippingRateError implements ShippingResultInterface
{
	protected $rawResponse;

	protected $errorMessage;

	public function __construct($message = null)
	{
		$this->errorMessage = (string)$message;
	}

	function setRawResponse($rawResponse)
	{
		$this->rawResponse = $rawResponse;
	}

	function getRawResponse()
	{
		return $this->rawResponse;
	}
}

?>
