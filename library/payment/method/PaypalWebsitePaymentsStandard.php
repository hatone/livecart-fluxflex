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
class PaypalWebsitePaymentsStandard extends ExternalPayment
{
	public function getUrl()
	{
		$url = 'https://www.' . ($this->getConfigValue('SANDBOX') ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr';

		$params = array();
		$params['business'] = $this->getConfigValue('EMAIL');
		$nr = 1;
		foreach ($this->details->getLineItems() as $item)
		{
			$name = array();
			$params['item_name_'.$nr] = (isset($item['sku']) && strlen($item['sku']) > 0 ? $item['sku'] . ' ' : ''). $item['name'];
			$params['quantity_'.$nr] = $item['quantity'];
			$params['amount_'.$nr++] = number_format($item['price'], 2, '.', '');
		}
		$params['currency_code'] = $this->details->currency->get();
		$params['custom'] = $this->details->invoiceID->get();
		$params['return'] = $this->returnUrl;
		$params['notify_url'] = $this->notifyUrl;
		$params['upload'] = 1; // Indicates the use of third party shopping cart
		$params['cmd'] = '_cart'; // for passing individual item details to PayPal set the cmd variable to _cart.

		$pairs = array();
		foreach ($params as $key => $value)
		{
			$pairs[] = $key . '=' . urlencode($value);
		}

		return $url . '?' . implode('&', $pairs);
	}

	public function notify($requestArray)
	{
		// assign posted variables to local variables
		$paymentStatus = $requestArray['payment_status'];
		$paymentAmount = $requestArray['mc_gross'];
		$paymentCurrency = $requestArray['mc_currency'];
		$txn_id = $requestArray['txn_id'];
		$receiverEmail = $requestArray['receiver_email'];
		$payerEmail = $requestArray['payer_email'];

		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';

		foreach ($requestArray as $key => $value)
		{
			if (is_array($value))
			{
				continue;
			}

			$value = urlencode(stripslashes($value));
			$req .= "&".$key."=".$value;
		}

		// check that receiver_email is your Primary PayPal email
		if (strtolower($receiverEmail) != strtolower($this->getConfigValue('EMAIL')))
		{
			throw new PaymentException('Invalid PayPal receiver e-mail');
		}

		// post back to PayPal system to validate
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ('www.' . ($this->getConfigValue('SANDBOX') ? 'sandbox.' : '') . 'paypal.com', 80, $errno, $errstr, 30);

		if (!$fp)
		{
			throw new PaymentException('Could not connect to PayPal server');
		}
		else
		{
			fputs ($fp, $header . $req);

			while (!feof($fp))
			{
				$res = fgets ($fp, 1024);

				if (is_numeric(strrpos($res, "VERIFIED")))
				{
					if (($paymentStatus != 'Completed') && !$this->getConfigValue('SANDBOX'))
					{
						throw new PaymentException('Payment is not completed');
					}
				}
				else if (is_numeric(strrpos($res, "INVALID")))
				{
					throw new PaymentException('Invalid response from PayPal');
				}
			}

			fclose ($fp);
		}

		$result = new TransactionResult();
		$result->gatewayTransactionID->set($requestArray['txn_id']);
		$result->amount->set($requestArray['mc_gross']);
		$result->currency->set($requestArray['mc_currency']);
		$result->rawResponse->set($requestArray);

		if ('Completed' == $requestArray['payment_status'])
		{
			$result->setTransactionType(TransactionResult::TYPE_SALE);
		}
		else
		{
			$result->setTransactionType(TransactionResult::TYPE_AUTH);
		}

		return $result;
	}

	public function getOrderIdFromRequest($requestArray)
	{
		return $requestArray['custom'];
	}

	public function getReturnUrlFromRequest($requestArray)
	{
		return $requestArray['return'];
	}

	public function isHtmlResponse()
	{
		return false;
	}

	public function getValidCurrency($currentCurrencyCode)
	{
		$currentCurrencyCode = strtoupper($currentCurrencyCode);
		$defCurrency = $this->getConfigValue('DEF_CURRENCY');
		if (!$defCurrency)
		{
			$defCurrency = 'USD';
		}
		return in_array($currentCurrencyCode, self::getSupportedCurrencies()) ? $currentCurrencyCode : $defCurrency;
	}

	public static function getSupportedCurrencies()
	{
		return array('USD', 'CAD', 'EUR', 'GBP', 'JPY', 'AUD', 'NZD', 'CHF', 'HKD', 'SGD', 'SEK', 'DKK', 'PLN', 'NOK', 'HUF', 'CZK', 'MXN', 'ILS');
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
