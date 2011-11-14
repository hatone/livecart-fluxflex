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

ClassLoader::import('framework.renderer.RendererException');

/**
 * Thrown when view file for a requested controller's action does not exist.
 *
 * @package	framework.renderer
 * @author Integry Systems
 */
class ViewNotFoundException extends RendererException
{
	/**
	 * Template path (view name)
	 */
	private $viewName = "";

	/**
	 * @param string $view Path of not found view
	 */
	public function __construct($view)
	{
		parent::__construct('Specified view (' . $view . ') was not found');
		$this->viewName = $view;
	}

	/**
	 * Gets view path
	 *
	 * @return string View path
	 */
	public function getView()
	{
		return $this->viewName;
	}
}

?>
