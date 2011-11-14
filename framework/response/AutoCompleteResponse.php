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

ClassLoader::import("framework.response.Response");

/**
 * Input field auto-complete data response
 *
 * @package	framework.response
 * @author Integry Systems
 */
class AutoCompleteResponse extends Response
{
	private $content = "";

	public function __construct($data)
	{
		if (!is_array($data))
		{
		  	throw new Exception('AutoCompleteResponse::__construct needs an array passed!');
		}
				
		$this->content = $data;
	}

	public function getData()
	{
		$listHtml = array('<ul>');
	   	
	  	$li = new HtmlElement('li');
		foreach ($this->content as $key => $value)
		{
			$li->setAttribute('id', $key);
			$li->setContent($value);
			$listHtml[] = $li->render();
		} 
	   
		$listHtml[] = '</ul>';

		return implode("\n", $listHtml);
	}
}

?>
