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

include_once(dirname(__file__) . '/../../abstract/ExpressPayment.php');
include_once(dirname(__file__) . '/../../method/library/paypal/PaypalCommon.php');

/**
 *
 * @package library.payment.method.express
 * @author Integry Systems
 */
class PaypalExpressCheckout extends ExpressPayment
{
	protected $data;

	public function getInitUrl($returnUrl, $cancelUrl, $sale = true)
	{
		$sandbox = $this->getConfigValue('sandbox') ? 'sandbox.' : '';
		$paypal = $this->getHandler('SetExpressCheckout');
		$paypal->setRecurringItems($this->details->getRecurringItems());
		$paypal->setParams($this->details->amount->get(), $returnUrl, $cancelUrl, $sale ? 'Sale' : 'Order', $this->details->currency->get());
		$paypal->execute();
		$this->checkErrors($paypal);
		$response = $paypal->getAPIResponse();

		return 'https://www.' . $sandbox . 'paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . $response->Token;
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function getTransactionDetails($request = null)
	{
		$paypal = $this->getHandler('GetExpressCheckoutDetails');
		$paypal->setParams($request ? $request['token'] : $this->data['token']);
		$paypal->execute();

		$this->checkErrors($paypal);

		$response = $paypal->getAPIResponse();
		$info = $response->GetExpressCheckoutDetailsResponseDetails->PayerInfo;

		$valueMap = array(

				'firstName' => $info->PayerName->FirstName,
				'lastName' => $info->PayerName->LastName,
				'companyName' => isset($info->PayerBusiness) ? $info->PayerBusiness : '',

				'address' => $info->Address->Street1 . (!empty($info->Address->Street2) ? ', ' . $info->Address->Street2 : ''),
				'city' => $info->Address->CityName,
				'state' => $info->Address->StateOrProvince,
				'country' => $info->Address->Country,
				'postalCode' => $info->Address->PostalCode,
				'email' => $info->Payer,
				// customer data
				'clientID' => $info->PayerID,
			);

		foreach ($valueMap as $key => $value)
		{
			$this->details->$key->set($value);
		}

		return $this->details;
	}

	private function checkErrors($paypal)
	{
		if ($paypal->success())
		{
			$response = $paypal->getAPIResponse();
			if (isset($response->Errors))
			{
				throw new PaymentException($response->Errors->LongMessage);
			}
		}
		else
		{
			throw new PaymentException($paypal->getAPIException()->getMessage());
		}
	}

	public function isCreditable()
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
		return false;
	}

	public function getValidCurrency($currentCurrencyCode)
	{
		$currentCurrencyCode = strtoupper($currentCurrencyCode);
		return in_array($currentCurrencyCode, self::getSupportedCurrencies()) ? $currentCurrencyCode : 'USD';
	}

	public static function getSupportedCurrencies()
	{
		return array('CAD', 'EUR', 'GBP', 'USD', 'JPY', 'AUD', 'NZD', 'CHF', 'HKD', 'SGD', 'SEK', 'DKK', 'PLN', 'NOK', 'HUF', 'CZK', 'MXN', 'ILS');
	}

	/**
	 *	Reserve funds on customers credit card
	 */
	public function authorize()
	{
		return $this->processAuth('DoExpressCheckoutPayment', 'Order');
	}

	/**
	 *	Capture reserved funds
	 */
	public function capture()
	{
		return $this->processCapture();
	}

	/**
	 *	Void the payment (issue full credit)
	 */
	public function void()
	{
		return $this->processVoid();
	}

	/**
	 *	Authorize and capture funds within one transaction
	 */
	public function authorizeAndCapture()
	{
		return $this->processAuth('DoExpressCheckoutPayment', 'Sale');
	}

	protected function processAuth($api, $type)
	{
		$paypal = $this->getHandler($api);

		$address = PayPalTypes::AddressType(

						$this->details->firstName->get() . ' ' . $this->details->lastName->get(),
						$this->details->address->get(), '' /* address 2 */,
						$this->details->city->get(), $this->details->state->get(),
						$this->details->postalCode->get(), $this->details->country->get(),
						$this->details->phone->get()

					);

		$personName = PayPalTypes::PersonNameType('', $this->details->firstName->get(), '', $this->details->lastName->get());

		$payerInfo = PayPalTypes::PayerInfoType($this->details->email->get(), $this->details->clientID->get(), 'verified', $personName, $this->details->country->get(), '', $address);

		$paymentDetails = PayPalTypes::PaymentDetailsType($this->details->amount->get(), $this->details->amount->get(), 0, 0, 0, $this->details->description->get(), $this->details->clientID->get(), $this->details->invoiceID->get(), '', '', '', array(), $this->details->currency->get());

		$paypal->setParams($type, $this->data['token'], $this->data['PayerID'], $paymentDetails);

		$paypal->execute($api);

		if ($paypal->success())
		{
			$response = $paypal->getAPIResponse();

			if (isset($response->Errors))
			{
				$error = isset($response->Errors->LongMessage) ? $response->Errors : $error = $response->Errors[0];

				return new TransactionError($error->LongMessage, $response);
			}
			else
			{
				$paymentInfo = $response->DoExpressCheckoutPaymentResponseDetails->PaymentInfo;

				$result = new TransactionResult();

				$result->gatewayTransactionID->set($paymentInfo->TransactionID);
				$result->amount->set($paymentInfo->GrossAmount);
				$result->currency->set($response->Currency);

				$result->rawResponse->set($response);

				if ('Sale' == $type)
				{
					$result->setTransactionType(TransactionResult::TYPE_SALE);
				}
				else
				{
					$result->setTransactionType(TransactionResult::TYPE_AUTH);
				}

				return $result;
			}
		}
		else
		{
			return $paypal->getAPIException();
		}
	}

	protected function processCapture()
	{
		return PaypalCommon::processCapture($this->details, $this->getHandler('DoCapture'));
	}

	protected function processVoid()
	{
		return PaypalCommon::processVoid($this);
	}

	public function getHandler($api)
	{
		PayPalBase::$isLive = !$this->getConfigValue('sandbox');

		return PaypalCommon::getHandler($this, $api);
	}

	public function createRecurringPaymentProfile()
	{
		// ini_set("soap.wsdl_cache_enabled", 0);
		$paypal = $this->getHandler('DoCreateRecurringPaymentsProfile');
		$paypal->setParams( $this->data['token']);

		$paypal->setRecurringItems($this->details->getRecurringItems());
		$executionResponse = $paypal->execute();

		if (is_array($executionResponse) && count($executionResponse) > 0 )
		{
			$express = ExpressCheckout::getInstanceByOrder($this->details->getOrder());
			$paymentData = @unserialize($express->paymentData->get());
			if (!is_array($paymentData))
			{
				$paymentData = array();
			}
			$paymentData['PayPalRecurringProfiles'] = $executionResponse;
			$express->paymentData->set(serialize($paymentData));
			$express->save();
		}
		return true;
	}

	public function cancelRecurring($recurringItemID = null)
	{
		$paypal = $this->getHandler('ManageRecurringPaymentsProfileStatus');

		$recurringItemIDs = array();
		if ($recurringItemID !== null)
		{
			$recurringItemIDs[] = $recurringItemID;
		}
		else
		{
			$recurringItemIDs = array_keys($this->data['PayPalRecurringProfiles']);
		}
		$executionResponse = array();
		foreach($recurringItemIDs as $recurringItemID)
		{
			if (
				array_key_exists($recurringItemID, $this->data['PayPalRecurringProfiles'])
				&& array_key_exists('CreateRecurringPaymentsProfileResponseDetails', $this->data['PayPalRecurringProfiles'][$recurringItemID])
				&& array_key_exists('ProfileID', $this->data['PayPalRecurringProfiles'][$recurringItemID]['CreateRecurringPaymentsProfileResponseDetails'])
			) {
				$profileID = $this->data['PayPalRecurringProfiles'][$recurringItemID]['CreateRecurringPaymentsProfileResponseDetails']['ProfileID'];
				$paypal->setRecurringProfileID($profileID);
				$paypal->setAction('Cancel');
				$executionResponse[$profileID] = $paypal->execute();
			}
		}
		$success = true;
		foreach($executionResponse as $ProfileID => $response)
		{
			if ($response->Ack != 'Success')
			{
				$success = false;
			}
		}
		return $success;
	}
}

?>
