/*************************************************************************************************
 * LiveCart																					  *
 * Copyright (C) 2007-2009 UAB "Integry Systems" (http://livecart.com)							*
 * All rights reserved																		   *
 *																							   *
 * This source file is a part of LiveCart software package and is protected by LiveCart license. *
 * The license text can be found in the license.txt file. In case you received a package without *
 * a license file, the license text is also available online at http://livecart.com/license	  *
 *************************************************************************************************//**
 *	@author Integry Systems
 */
 
/**
 * This function converst a file path into a valid variable name
 *
 * @example http://livecart/public/javascript/library/include.js => http_livecart_public_javascript_library_include_js
 *
 */

var ClassLoader = {};

ClassLoader.createFileIdentifier = function(filename)
{
	var base = document.getElementsByTagName("base")[0].href;

	return filename.replace(new RegExp('^' + base + 'javascript/', ''), '').replace(/[^\w]+/g, '_')
}


require_once = function(file)
{
	if(!window.loadedScripts)
	{
		window.loadedScripts = {};

		var scripts = document.getElementsByTagName('script');
		for(var i = 0; i < scripts.length; i++)
		{
			var handle = ClassLoader.createFileIdentifier(scripts[i].src);
			window.loadedScripts[handle] = true;
		}
	}

	var handle = ClassLoader.createFileIdentifier(file);
	if(!window.loadedScripts[handle])
	{
		var head = document.getElementsByTagName("head")[0];
		head.appendChild(document.createTextNode("\n"));

		var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = (!file.match(/^http/) ? 'javascript/' : '') + file;
		head.appendChild(script);

		window.loadedScripts[handle];
	}
}