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
 
var SectionExpander = Class.create();

SectionExpander.prototype = 
{
	/**
	 * SectionExpander constructor
	 */
	initialize: function(parent)
	{
		var sectionList = document.getElementsByClassName('expandingSection', $(parent));

		for (var i = 0; i < sectionList.length; i++)
		{
			var legend = sectionList[i].down('legend');
			if (legend)
			{
				legend.onclick = this.handleLegendClick.bindAsEventListener(this);
			}
		}
	},
	
	unexpand: function(parent)
	{
		Element.addClassName($(parent), 'expanded');
	},

	/**
	 * Legend element click handler
	 * Toggles fieldsets content visibility
	 */
	handleLegendClick: function(evt)
	{
		Element.toggleClassName(Event.element(evt).parentNode, 'expanded');		
		tinyMCE.execCommand("mceResetDesignMode");
	}
}