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
class iDEALeasy extends ExternalPayment
{
	public function getUrl()
	{
		return 'https://internetkassa.abnamro.nl/ncol/prod/orderstandard.asp';
	}

	public function isPostRedirect()
	{
		return true;
	}

	public function isNotify()
	{
		return false;
	}

	public function getPostParams()
	{
		$params = array();

		// order info
		$params['PSPID'] = $this->getConfigValue('account');
		$params['orderID'] = $this->details->invoiceID->get();
		$params['COM'] = $this->getConfigValue('description');
		$params['amount'] = $this->details->amount->get() * 100;
		$params['currency'] = 'EUR';
		$params['language'] = 'NL_NL';
		$params['PM'] = 'iDEAL';

		// customer information
		$params['CN'] = $this->details->getName();
		$params['owneraddress'] = $this->details->address->get();
		$params['ownertown'] = $this->details->city->get();
		$params['ownerzip'] = $this->details->postalCode->get();
		$params['ownercty'] = $this->details->country->get();
		$params['EMAIL'] = $this->details->email->get();

		// needed?
		$params['submit1'] = 'submit';

		return $params;
	}

	public function notify($requestArray)
	{
		return null;
	}

	public function getOrderIdFromRequest($requestArray)
	{
		return null;
	}

	public function isHtmlResponse()
	{
		return true;
	}

	public function getValidCurrency($currentCurrencyCode)
	{
		return 'EUR';
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
