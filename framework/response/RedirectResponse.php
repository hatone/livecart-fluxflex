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

ClassLoader::import('framework.response.Response');

/**
 * Class for creating redirect response.
 *
 * @package	framework.response
 * @author	Integry Systems
 */
class RedirectResponse extends Response
{
	/**
	 * @param string $url URL location
	 */
	public function __construct($url)
	{
		$this->setRedirectUrl($url);
	}

	/**
	 * Sets URL to redirect to
	 *
	 * @param string $url URL location
	 * @return void
	 */
	public function setRedirectURL($url)
	{
		$this->setHeader('Location', $url);
	}

	/**
	 * Gets URL of redirect
	 *
	 * @return string URL location
	 */
	public function getRedirectURL()
	{
		return $this->getHeader('Location');
	}

	public function getData()
	{
		return ;
	}
}

?>
