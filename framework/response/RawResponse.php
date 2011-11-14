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

ClassLoader::import('framework.response.*');

/**
 * Class for creating response from raw output.
 *
 * @package	framework.response
 * @author	Integry Systems 
 */
class RawResponse extends Response
{
	/**
	 * Stores content
	 */
	private $content;

	/**
	 * @param string $content Raw output
	 */
	public function __construct($content = '')
	{
		$this->setHeader('Content-type', 'text/html');
		$this->setContent($content);
	}

	/**
	 * Sets content to response
	 *
	 * @param string $content Content
	 * @param boolean $append True to append content, false to replace
	 * @return void
	 */
	public function setContent($content, $append = false)
	{
		if ($append)
		{
			$this->content .= $content;
		}
		else
		{
			$this->content = $content;
		}
	}

	/**
	 * Gets content from response
	 *
	 * @return string Content of response
	 */
	public function getContent()
	{
		return $this->content;
	}


	public function getData()
	{
		return $this->content;
	}

}

?>
