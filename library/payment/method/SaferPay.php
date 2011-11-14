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

include_once(dirname(__file__) . '/../abstract/ExternalPayment.php');

/**
 *
 * @package library.payment.method
 * @author Integry Systems
 */
class SaferPay extends ExternalPayment
{
	public function getUrl()
	{
		$params = array();

		$params['ACCOUNTID'] = $this->getConfigValue('ACCOUNTID');
		$params['DESCRIPTION'] = $this->getConfigValue('DESCRIPTION');
		$params['ORDERID'] = $this->details->invoiceID->get();
		$params['AMOUNT'] = $this->details->amount->get() * 100;
		$params['CURRENCY'] = $this->details->currency->get();

		$params['NOTIFYURL'] = $this->notifyUrl;
		$params['SUCCESSLINK'] = $this->returnUrl;
		$params['FAILLINK'] = $this->siteUrl;
		$params['BACKLINK'] = $this->siteUrl;

		$params['ALLOWCOLLECT'] = 'no';
		$params['DELIVERY'] = 'no';

		$pairs = array();
		foreach ($params as $key => $value)
		{
			$pairs[] = $key . '=' . urlencode($value);
		}

		$url = file_get_contents('https://www.saferpay.com/hosting/CreatePayInit.asp?' . implode('&', $pairs));

		if (substr($url, 0, 5) == 'https')
		{
			return $url;
		}
	}

	public function notify($requestArray)
	{
		$requestArray = $_REQUEST;

		$result = new TransactionResult();
		$result->gatewayTransactionID->set($requestArray['ID']);
		$result->amount->set($requestArray['AMOUNT'] / 100);
		$result->currency->set($requestArray['CURRENCY']);
		$result->rawResponse->set($requestArray);
		$result->setTransactionType(TransactionResult::TYPE_SALE);

		return $result;
	}

	public function getOrderIdFromRequest($requestArray)
	{
		$url = 'https://www.saferpay.com/hosting/VerifyPayConfirm.asp?DATA=' . urlencode($_REQUEST['DATA']) . '&SIGNATURE=' . urlencode($requestArray['SIGNATURE']);
		$result = file_get_contents($url);

		if (substr($result, 0, 3) == 'OK:')
		{
			$data = stripslashes(urldecode($_REQUEST['DATA']));
			preg_match_all('/([A-Z]+)\="(.*)"/msU', $data, $matches);
			$data = array_combine($matches[1], $matches[2]);
			$_REQUEST = array_merge($_REQUEST, $data);
		}

		return array_shift(explode('_', $_REQUEST['ORDERID']));
	}

	public function isHtmlResponse()
	{
		return true;
	}

	public function getValidCurrency($currentCurrencyCode)
	{
		return $currentCurrencyCode;
	}

	public function isVoidable()
	{
		return false;
	}

	public function void()
	{
		return false;
	}
}

?>
