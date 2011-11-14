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

include_once('phppaypalpro/paypal_base.php');

/**
 *
 * @package library.payment.method.library
 * @author Integry Systems 
 */
class PaypalCommon
{
	/**
	 *	Get AVS (address verification) status by PayPal processor AVS code
	 *
	 *	First value is address verification and the second is ZIP verification
	 *	true - verification passed
	 *	false - verification failed
	 *	null - verification unavailable
	 *
	 *	@return bool
	 */
	public static function getAVSbyCode($code)
	{
		$avs = array(
			
					'A' => array(true, false),
					
					'B' => array(true, false),
					
					'C' => array(false, false),
					
					'D' => array(true, true),
					
					'E' => array(null, null),
					
					'F' => array(true, true),
					
					'G' => array(null, null),
					
					'I' => array(null, null),
					
					'N' => array(false, false),
					
					'P' => array(false, true),
					
					'R' => array(null, null),
					
					'S' => array(null, null),
					
					'U' => array(null, null),
					
					'W' => array(false, true),
					
					'X' => array(true, true),
					
					'Y' => array(true, true),
					
					'Z' => array(false, true),
		
				);		
	
		if (isset($avs[$code]))
		{
			return $avs[$code];
		}
		else
		{
			return array(null, null);
		}	
	}
	
	/**
	 *	Get card security code verification status by Paypal code
	 *
	 *	true - verification passed
	 *	false - verification failed
	 *	null - verification unavailable
	 *
	 *	@return bool
	 */
	public static function getCVVByCode($code)
	{
		if ('N' == $code)
		{
			return false;
		}
		else if ('M' == $code)
		{
			return true;
		}
		else
		{
			return null;
		}		
	}
	
	public function getValidCurrency($currentCurrencyCode)
	{
		$currentCurrencyCode = strtoupper($currentCurrencyCode);
		return in_array($currentCurrencyCode, self::getSupportedCurrencies()) ? $currentCurrencyCode : 'USD';
	}	
	
	public static function getSupportedCurrencies()
	{
		return array('CAD', 'EUR', 'GBP', 'USD', 'JPY', 'AUD', 'NZD', 'CHF', 'HKD', 'SGD', 'SEK', 'DKK', 'PLN', 'NOK', 'HUF', 'CZK');
	}
	
	/**
	 *  DoCapture implementation for all PayPal payment classes
	 */
	public function processCapture(TransactionPayment $handler)
	{
		$details = $handler->getDetails();
		$paypal = $handler->getHandler('DoCapture');
		
		$paypal->setParams($details->gatewayTransactionID->get(), $details->amount->get(), $details->currency->get(), $details->isCompleted->get() ? 'Complete' : 'NotComplete', '', $details->invoiceID->get());
		
		$paypal->execute();
		
		if ($paypal->success())
		{
			$response = $paypal->getAPIResponse();

			if (isset($response->Errors))
			{
				return new TransactionError($response->Errors->LongMessage, $response);
			}
			else
			{
				$result = new TransactionResult();

				$details = $response->DoCaptureResponseDetails->PaymentInfo;
			
				$result->gatewayTransactionID->set($details->TransactionID);
				$result->amount->set($details->GrossAmount);
				$result->currency->set($response->Currency);

				$result->rawResponse->set($response);
				$result->setTransactionType(TransactionResult::TYPE_CAPTURE);

				return $result;
			}
		}
		else
		{
			return $paypal->getAPIException();
		}			
	}

	/**
	 *  DoVoid implementation for all PayPal payment classes
	 */
	public function processVoid(TransactionPayment $handler)
	{
		$details = $handler->getDetails();
		$paypal = $handler->getHandler('DoVoid');
		
		$paypal->setParams($details->gatewayTransactionID->get(), '');
		
		$paypal->execute();
		
		if ($paypal->success())
		{
			$response = $paypal->getAPIResponse();

			if (isset($response->Errors))
			{
				return new TransactionError($response->Errors->LongMessage, $response);
			}
			else
			{
				$result = new TransactionResult();

				$result->rawResponse->set($response);
				$result->setTransactionType(TransactionResult::TYPE_VOID);

				return $result;
			}
		}
		else
		{
			return $paypal->getAPIException();
		}		
	}	
	
	public function getHandler(TransactionPayment $handler, $api)
	{
		set_time_limit(0);
		
		$username = $handler->getConfigValue('username');
		$password = $handler->getConfigValue('password');
		$signature = $handler->getConfigValue('signature');
		
		$wpp = new WebsitePaymentsPro();
		$wpp->prepare($username, $password, $signature);		
		
		return $wpp->selectOperation($api);
	}
}

?>
