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

ClassLoader::import("framework.renderer.Renderable");

/**
 * Renderable object interface
 *
 * @package	framework.renderer
 * @author Integry Systems
 */
interface Renderable
{
	/**
	 * Renders self into renderer using view if needed
	 *
	 * @param Renderer $renderer Renderer to render into
	 * @param string $view Path to view
	 * @return string Rendered output
	 * @throws ViewNotFoundException if view does not exists
	 */
	public function render(Renderer $renderer, $view);
}

?>
