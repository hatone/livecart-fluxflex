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

/**
 *
 * @package library.payment
 * @author Integry Systems
 */
class PaymentMethodManager
{
	public function getCreditCardHandlerList()
	{
		return self::getPaymentHandlerList(dirname(__FILE__) . '/method/cc');
	}

	public function getExpressPaymentHandlerList()
	{
		return self::getPaymentHandlerList(dirname(__FILE__) . '/method/express');
	}

	public function getRegularPaymentHandlerList()
	{
		return self::getPaymentHandlerList(dirname(__FILE__) . '/method');
	}

	private function getPaymentHandlerList($dir)
	{
		$ret = array();

		foreach (new DirectoryIterator($dir) as $method)
		{
			if ($method->isFile() && substr($method->getFileName(), 0, 1) != '.')
			{
				$ret[] = basename($method->getFileName(), '.php');
			}
		}

		sort($ret);

		return $ret;
	}
}

?>
