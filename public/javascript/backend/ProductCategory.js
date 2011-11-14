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

if (!Backend.ProductCategory)
{
	Backend.ProductCategory = {}
}

Backend.ProductCategory = function(container, product, categories)
{
	this.container = container;
	this.product = product;

	this.findUsedNodes();
	this.bindEvents();

	if (categories)
	{
		for (k = 0; k < categories.length; k++)
		{
			this.addCategory(categories[k].ID, this.getCategoryPath(categories[k]));
		}
	}
}

Backend.ProductCategory.prototype =
{
	findUsedNodes: function()
	{
		this.changeCategoryLink = this.container.down('.changeMainCategory').down('a');
		this.addCategoryLink = this.container.down('.addAdditionalCategory').down('a');
		this.mainCategoryName = this.container.down('.mainCategory');
		this.additionalCategories = this.container.down('.additionalCategories');
		this.categoryTemplate = this.container.down('.categoryTemplate');
	},

	bindEvents: function()
	{
		Event.observe(this.changeCategoryLink, 'click', this.initChangeCategory.bindAsEventListener(this));
		Event.observe(this.addCategoryLink, 'click', this.initAddCategory.bindAsEventListener(this));
	},

	getCategoryPath: function(category)
	{
		var path = '';

		if (category.ParentNode)
		{
			path = this.getCategoryPath(category.ParentNode) + ' &gt; ';
		}

		path += category.name_lang;

		return path;
	},

	initChangeCategory: function(e)
	{
		Event.stop(e);
		var selector = new Backend.Category.PopupSelector(
			function(categoryID, pathAsText, path) { this.saveChangeCategory(categoryID, pathAsText, path, selector); }.bind(this),
			null,
			this.product.Category.ID
		);
	},

	saveChangeCategory: function(categoryID, pathAsText, path, selector)
	{
		this.product.Category.ID = categoryID;
		this.mainCategoryName.down('.categoryName').update(pathAsText);
		new LiveCart.AjaxRequest(Backend.Router.createUrl('backend.productCategory', 'saveMainCategory', {id: this.product.ID, categoryId: categoryID}), this.mainCategoryName.down('.progressIndicator'),
				function()
				{
					selector.window.close();
					new Effect.Highlight(this.mainCategoryName);
				}.bind(this));
	},

	initAddCategory: function(e)
	{
		Event.stop(e);
		var selector = new Backend.Category.PopupSelector(
			this.saveAddCategory.bind(this),
			null,
			this.product.Category.ID
		);
	},

	saveAddCategory: function(categoryID, pathAsText, path, selector)
	{
		new LiveCart.AjaxRequest(Backend.Router.createUrl('backend.productCategory', 'addCategory', {id: this.product.ID, categoryId: categoryID}), this.additionalCategories.up('fieldset').down('legend').down('.progressIndicator'),
			function(originalRequest)
			{
				if (originalRequest.responseData.data)
				{
					var el = this.addCategory(categoryID, pathAsText);
					new Effect.Highlight(el);
					this.updateTabCount();
				}
			}.bind(this));
	},

	addCategory: function(categoryID, pathAsText)
	{
		var node = this.categoryTemplate.cloneNode(true);
		this.additionalCategories.appendChild(node);

		node.down('.categoryName').update(pathAsText);
		node.show();
		node.removeClassName('categoryTemplate');

		Event.observe(node.down('.recordDelete'), 'click', this.initDeleteCategory.bindAsEventListener(this));

		node.categoryID = categoryID;

		return node;
	},

	initDeleteCategory: function(e)
	{
		Event.stop(e);
		var node = Event.element(e).up('li');
		new LiveCart.AjaxRequest(Backend.Router.createUrl('backend.productCategory', 'delete', {id: this.product.ID, categoryId: node.categoryID}), node.down('.progressIndicator'), function() { node.parentNode.removeChild(node); this.updateTabCount(); }.bind(this));
	},

	updateTabCount: function()
	{
		var count = this.additionalCategories.getElementsByTagName('li').length + 1;
		var tabControl = TabControl.prototype.getInstance("productManagerContainer", false);
		tabControl.setCounter('tabProductCategories', count);
	}
}