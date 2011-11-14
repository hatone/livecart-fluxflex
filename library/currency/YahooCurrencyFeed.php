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
 * @package library.currency
 * @author Integry Systems 
 */
class YahooCurrencyFeed extends CurrencyRateFeed
{
	public static function getName()
	{
	  	return 'Yahoo!';
	}	  	  
	
	protected function getFeedUrl()
	{
	  	return 'http://download.finance.yahoo.com/d/quotes.csv?s=' . $this->baseCurrency .
		  	   $this->targetCurrency . '=X&f=sl1d1t1ba&e=.csv';
	}  
	
	protected function parseData($feedData)
	{
		$data = explode(',', $feedData);
		if (is_numeric($data[1]))
		{
		  	$this->setRate($data[1]);
		}  	
	}
}

?>
