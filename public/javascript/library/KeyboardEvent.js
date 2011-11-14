/*************************************************************************************************
 * LiveCart																					  *
 * Copyright (C) 2007-2009 UAB "Integry Systems" (http://livecart.com)							*
 * All rights reserved																		   *
 *																							   *
 * This source file is a part of LiveCart software package and is protected by LiveCart license. *
 * The license text can be found in the license.txt file. In case you received a package without *
 * a license file, the license text is also available online at http://livecart.com/license	  *
 *************************************************************************************************//**
 * KeyboardEvent's task is to provide cross-browser suport for handling keyboard
 * events. It provides function to get current button code and char, shift status, etc
 *
 * @todo Caps lock support
 *
 * @author   Integry Systems
 *
 */
var KeyboardEvent = Class.create();
KeyboardEvent.prototype = {
	/**
	 * Tab key
	 *
	 * @var int
	 */
	KEY_TAB:	9,

	/**
	 * Enter key
	 *
	 * @var int
	 */
	KEY_ENTER:  13,

	/**
	 * Shift key
	 *
	 * @var int
	 */
	KEY_SHIFT:  16,

	/**
	 * Escape key
	 *
	 * @var int
	 */
	KEY_ESC:	27,

	/**
	 * Up key
	 *
	 * @var int
	 */
	KEY_UP:	 38,

	/**
	 * Down key
	 *
	 * @var int
	 */
	KEY_DOWN:   40,

	/**
	 * Left key
	 *
	 * @var int
	 */
	KEY_LEFT:	 37,

	/**
	 * Right key
	 *
	 * @var int
	 */
	KEY_RIGHT:   39,

	/**
	 * Delete key
	 *
	 * @var int
	 */
	KEY_DEL:	46,

	/**
	 * Constructor
	 *
	 * @param WindowEvent e Event object (for explorer it will be auto-detected. If you decide to pass it anyway then passed event will be used)
	 *
	 * @access public
	 */
	initialize: function(e)
	{
		if (!e && window.event)
		{
			e = window.event; // IE
			e.target = e.srcElement;
		}

		this.event = e;
	},
	
	init: function(e)
	{
		return new KeyboardEvent(e);
	},

	/**
	 * Determines which key (number) was pressed
	 *
	 * @access public
	 *
	 * @return int Key number
	 */
	getKey: function()
	{
		return this.event.which ? this.event.which : ( this.event.keyCode ? this.event.keyCode : ( this.event.charCode ? this.event.charCode : 0 ) );
	},


	/**
	 * Determines which char was pressed. It will also check if shift button
	 * was hold and return upercase if it was. So far no support for caps lock
	 *
	 * @access public
	 *
	 * @todo Caps lock support
	 *
	 * @return int Key number
	 */
	getChar: function()
	{
		var string = String.fromCharCode(this.getKey());

		if(!this.isShift()) string = string.toLowerCase();

		return string;
	},

	/**
	 * Check if shift was pressed while inputing the letter
	 *
	 * @access public
	 *
	 * @return bool
	 */
	isShift: function()
	{
		return this.event.shiftKey || ( this.event.modifiers && ( this.event.modifiers & 4 ) );;
	},

	isPrintable: function()
	{
		var key = this.getKey();

		// [A-Za-z0-9]
		return (key > 64 && key < 91) || (key > 96 && key < 123) || (key > 47 && key < 58);
	},

	/**
	 * Deselects any window text (except in controls)
	 *
	 * @access public
	 */
	deselectText: function()
	{
		if (document.selection)
		{
			document.selection.empty();
		}
		else if (window.getSelection)
		{
			window.getSelection().removeAllRanges();
		}
	},

	/**
	 * Get cursor position in the text
	 *
	 * @access public
	 */
	getCursorPosition: function()
	{
		if(document.selection)
		{
			return document.selection;
		}
		else if(this.event.target.selectionStart)
		{
			return this.event.target.selectionStart;
		}
		else
		{
			return false;
		}
	},
	
	isEnter: function()
	{
		return this.getKey() == this.KEY_ENTER;
	}
}

