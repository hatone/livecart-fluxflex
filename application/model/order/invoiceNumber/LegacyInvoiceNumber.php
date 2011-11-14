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

ClassLoader::import("application.model.order.InvoiceNumberGenerator");

/**
 * Legacy invoice numbers (pre 1.3.0)
 * Equal to order database ID's, so they're not guaranteed to be even sequential
 *
 * @package application.model.order
 * @author Integry Systems <http://integry.com>
 */
class LegacyInvoiceNumber extends InvoiceNumberGenerator
{
	private $selectFilter;

	public function getNumber()
	{
		return $this->order->getID();
	}
}

?>
