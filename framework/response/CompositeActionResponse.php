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
ClassLoader::import('framework.renderer.Renderable');

/**
 * Allows CompositeResponse to set one renderable response
 *
 * @package	framework.response
 * @author Integry Systems
 */
class CompositeActionResponse extends CompositeResponse implements Renderable
{
	protected $mainResponse;
	
	public function setMainResponse(Renderable $response)
	{
		$this->mainResponse = $response;
	}

	public function render(Renderer $renderer, $view)
	{
		if (!$this->mainResponse)
		{
			$this->mainResponse = new ActionResponse();
		}
		
		foreach ($this->getData() as $key => $value)
		{
			$this->mainResponse->set($key, $value);
		}
		
		return $this->mainResponse->render($renderer, $view);
	}

	public function getData()
	{
		return $this->data;
	}

}

?>
