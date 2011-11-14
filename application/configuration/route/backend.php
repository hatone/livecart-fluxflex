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
 * Application request routing configuration
 *
 * @package application.configuration.route
 * @author Integry Systems
 */

$handle = '[^\.\047]{0,}';

$routes = array(
					// backend
					array("backend", array('controller' => 'backend.index', 'action' => 'index'), array()),
					array("admin", array('controller' => 'backend.index', 'action' => 'index'), array()),

					// contact form
					array("contact", array('controller' => 'contactForm', 'action' => 'index'), array()),

					// sitemaps
					array("sitemap-:type-:id", array('controller' => 'sitemap', 'action' => 'sitemap'), array("id" => "[0-9]+")),

					// category URLs
					array("shop/:cathandle.:id", array('controller' => 'category', 'action' => 'index'), array("cathandle" => $handle, "id" => "[0-9]+")),
					array("shop/:cathandle.:id/:page", array('controller' => 'category', 'action' => 'index'), array("cathandle" => $handle, "id" => "[0-9]+","page" => "[0-9_]+")),

					array("shop/:cathandle.:id/:filters", array('controller' => 'category', 'action' => 'index'), array("cathandle" => $handle, "id" => "[0-9]+", "filters" => "([,]{0,1}[^,\047]+\-[vmps]{0,1}[0-9]{0,})*")),
					array("shop/:cathandle.:id/:filters/:page", array('controller' => 'category', 'action' => 'index'), array("cathandle" => $handle, "id" => "[0-9]+","page" => "[0-9_]+", "filters" => "[^\047]+?")),

					// static pages
					array(":handle.html", array('controller' => 'staticPage', 'action' => 'view'), array("handle" => '[^\047]{0,}')),

					// news pages
					array("news/:handle.:id", array('controller' => 'news', 'action' => 'view'), array("handle" => $handle, "id" => "[0-9]+")),

					// product pages
					array(":producthandle.:id", array('controller' => 'product', 'action' => 'index'), array("producthandle" => $handle, "id" => "[0-9]+")),
					array("product/reviews/:id/:page", array('controller' => 'product', 'action' => 'reviews'), array("cathandle" => $handle, "id" => "[0-9]+","page" => "[0-9_]+")),

					// manufacturer pages
					array("manufacturer/:handle-:id", array('controller' => 'manufacturers', 'action' => 'view'), array("handle" => $handle, "id" => "[0-9]+")),

					// xml export
					array("xml/:module.xml", array('controller' => 'xml', 'action' => 'export'), array("module" => '[-a-zA-Z0-9]+')),

					// default rules
					array("", array("controller" => "index", "action" => "index"), array()),
					array(":controller", array("action" => "index"), array()),
					array(":controller/:id", array("action" => "index"), array("id" => "-?[0-9]+")),
					array(":controller/:action", array(), array()),
					array(":controller/:action/:id", array(), array("id" => "-?[0-9]+")),
					array(":controller/:action/:mode/:id", array(), array("id" => "[0-9]+", "mode" => "create|modify")),

					// special case for passing a language code as an ID
					array(":controller/:action/:id", array(), array('id' => "[a-zA-Z]{2}")),
					array(":controller/:action/:id", array(), array("id" => "_id_")),

					// checkout
					array("checkout/:action/:id", array('controller' => 'checkout'), array("id" => "[-A-Za-z0-9]+")),

				);

// custom routes
$routeDir = ClassLoader::getRealPath('storage.configuration.route');
foreach ((array)glob($routeDir . '/*.php') as $file)
{
	if ($file)
	{
		include $file;
	}
}

// language index page
$this->router->connect(':requestLanguage', array('controller' => 'index', 'action' => 'index'), array('requestLanguage' => "[a-zA-Z]{2}"));

/*
file_put_contents($routeCache, '<?php return unserialize(\'' . str_replace("'", "\'", serialize($this->router->getRoutes())) . '\'); ?>');
*/

?>
