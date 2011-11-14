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

ClassLoader::import('library.activerecord.ARFeed');
ClassLoader::import('application.model.order.CustomerOrder');

/**
 * Order data feed
 *
 * @author Integry Systems
 * @package application.controller
 */
class CustomerOrderFeed extends ARFeed
{
	protected $productFilter;

	public function __construct(ARSelectFilter $filter)
	{
		parent::__construct($filter, 'CustomerOrder', array('User'));
	}

	protected function postProcessData()
	{
	}
}

?>
