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
abstract class CurrencyRateFeed
{
	/**
	 *  Base currency code (ex: USD)
	 */
	protected $baseCurrency;

	/**
	 *  Code of the currency, which rate is being retrieved (ex: LTL)
	 */
	protected $targetCurrency;
	
	/**
	 *  Currency exchange rate (how many items of target currency can be bought for one item of base currency)
	 *  Ex: USD/LTL = 2.63
	 */
	protected $rate;
	
	public function __construct($baseCurrency, $targetCurrency)
	{
		$this->baseCurrency = $baseCurrency;
		$this->targetCurrency = $targetCurrency;
	} 
	
	public abstract static function getName();
	
	protected abstract function getFeedUrl();
	
	protected abstract function parseData($feedData);

	public function downloadData()
	{	
	  	$url = $this->getFeedUrl();
	  	
		// $feedData = .. download ..
	  	
	  	$this->parseData($feedData);	  	
	}
	   
	protected function setRate($rate)
	{
	  	$this->rate = $rate;
	}
	
	protected function rateTime($time)
	{
	  	$this->rateTime = $time;
	}	 
}

?>
