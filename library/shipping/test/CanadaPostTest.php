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

include_once('TestShipping.php');
ClassLoader::import('library.shipping.method.CanadaPostShipping');

/**
 *
 * @package library.shipping.test
 * @author Integry Systems
 */
class CanadaPostTest extends TestShipping
{
	public function setUp()
	{
		$this->post = new CanadaPostShipping();
		$this->post->setConfigValue('merchantID', 'CPC_DEMO_XML');
		$this->post->setSourceZip('m1p1c0');
		$this->post->setWeight(1.500);
	}

	public function testUSRates()
	{
		$post = $this->post;

		$post->setDestCountry('US');
		$post->setDestZip('90210');
		$post->setDestState('CA');

		$rates = $post->getAllRates();
		$this->assertTrue($rates instanceof ShippingRateSet);
		$this->assertTrue(count($rates->getRates()) > 0);

		// get by sedrvice
		$post->setService('Expedited US Commercial');
		$rates = $post->getRates();
		$this->assertTrue($rates instanceof ShippingRateSet);
		$this->assertEqual(count($rates->getRates()), 1);
	}

	public function testOverWeight()
	{
		$post = $this->post;

		$post->setWeight(1500);
		$post->setDestCountry('US');
		$post->setDestZip('90210');
		$post->setDestState('CA');

		$rates = $post->getAllRates();
		$this->assertTrue($rates instanceof ShippingRateError);
	}

	public function testDomestic()
	{
		$post = $this->post;

		$post->setDestCountry('CA');
		$post->setDestZip('V6G1J3');

		$rates = $post->getAllRates();
		$this->assertTrue($rates instanceof ShippingRateSet);
		$this->assertTrue(count($rates->getRates()) >= 3);
	}

	public function testLocal()
	{
		$post = $this->post;
		$post->setDestCountry('CA');
		$post->setDestZip('M1P1C0');

		$rates = $post->getAllRates();
		$this->assertTrue($rates instanceof ShippingRateSet);
		$this->assertEqual(count($rates->getRates()), 3);
	}

	public function testInternational()
	{
		$post = $this->post;
		$post->setDestCountry('LT');
		$post->setWeight(15);

		$rates = $post->getAllRates();

		$this->assertTrue($rates instanceof ShippingRateSet);
	}

	public function testInvalidDest()
	{
		$post = $this->post;
		$post->setDestCountry('CA');
		$post->setDestZip('H0H0H0');

		$rates = $post->getAllRates();
		$this->assertTrue($rates instanceof ShippingRateError);
	}

	/*
	public function testInvalidUSState()
	{
		$post = $this->post;
		$post->setDestCountry('US');
		$post->setDestState('XX');
		$post->setDestZip('99999');

		$rates = $post->getAllRates();
		var_dump($rates);
		$this->assertTrue($rates instanceof ShippingRateError);
	}
	*/
}

?>
