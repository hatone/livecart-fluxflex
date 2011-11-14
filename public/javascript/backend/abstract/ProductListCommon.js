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

Backend.ProductListCommon = {}
Backend.ProductListCommon.Product = {}

Backend.ProductListCommon.Product.activeListCallbacks = function(ownerID)
{
	this.ownerID = ownerID;
}

Backend.ProductListCommon.Product.activeListCallbacks.prototype =
{
	beforeSort: function(li, order)
	{
		return Backend.Router.createUrl(this.callbacks.controller, 'sort', {target: this.ul.id, id: this.callbacks.ownerID}) + "&" + order;
	}
}