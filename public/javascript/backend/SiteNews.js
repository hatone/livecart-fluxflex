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

Backend.SiteNews = Class.create();
Backend.SiteNews.prototype =
{
	initialize: function(newsList, container, template)
	{
		if ($("addNewsLink"))
		{
			Element.observe("addNewsLink", "click", function(e)
			{
				Event.stop(e);
				Backend.SiteNews.prototype.showAddForm();
			});

			Element.observe("addNewsCancelLink", "click", function(e)
			{
				Event.stop(e);
				Backend.SiteNews.prototype.hideAddForm();
			});
		}

		ActiveList.prototype.getInstance('newsList', {
			 beforeEdit:	 function(li)
			 {
				 if (!this.isContainerEmpty(li, 'edit'))
				 {
					 li.handler.cancelEditForm();
					 return;
				 }
				 li.handler.showEditForm();
				 return false;
			 },
			 beforeSort:	 function(li, order)
			 {
				 return $('sortUrl').innerHTML + '?draggedId=' + this.getRecordId(li) + '&' + order
			 },
			 beforeDelete:   function(li)
			 {
				 if (confirm($('confirmDelete').innerHTML)) return $('deleteUrl').innerHTML + this.getRecordId(li)
			 },
			 afterEdit: function(li, response) { li.handler.update(response);},
			 afterSort: function(li, response) {  },
			 afterDelete: function(li, response)
			 {
				 try
				 {
					response = eval('(' + response + ')');
				 }
				 catch(e)
				 {
					return false;
				 }
			 }
		 }, []);

		var
			match = Backend.getHash().match(/news_(\d+)/),
			id=null;
		if(match)
		{
			id = match.pop();
		}
		newsList.each(function(id, el)
		{
			var
				entry = new Backend.SiteNews.PostEntry(container, template, el, false);
			if(id && entry.data.ID == id)
			{
				entry.showEditForm();
			}
		}.bind(this, id));
		ActiveList.prototype.getInstance('newsList').touch(true);
	},

	showAddForm: function()
	{
		$H($('newsList').getElementsByTagName('li')).each(function(li)
		{
			if (li && li[1] && li[1].handler)
			{
				li[1].handler.cancelEditForm();
			}
		});

		var menu = new ActiveForm.Slide('newsMenu');
		menu.show("addNews", 'addNews');
	},

	hideAddForm: function()
	{
		var menu = new ActiveForm.Slide('newsMenu');
		menu.hide("addNews", 'addNews');
	}
}

Backend.SiteNews.PostEntry = Class.create();
Backend.SiteNews.PostEntry.prototype =
{
	data: null,

	node: null,

	list: null,

	initialize: function(container, template, data, highlight, isNew)
	{
		this.data = data;

		this.list = ActiveList.prototype.getInstance('newsList');

		this.node = this.list.addRecord(data.ID, template.innerHTML, highlight);

		if (isNew)
		{
			this.node.parentNode.insertBefore(this.node, this.node.parentNode.firstChild);
		}

		this.updateHtml();

		this.node.handler = this;

		Element.show(this.node);
	},

	showEditForm: function()
	{
		Backend.SiteNews.prototype.hideAddForm();

		var nodes = this.node.parentNode.getElementsByTagName('li');
		$H(nodes).each(function(li)
		{
			if (li && li[1] && li[1].handler && li != this.node)
			{
				li[1].handler.cancelEditForm();
			}
		});

		var form = $('newsForm').cloneNode(true);
		this.node.down('div.formContainer').appendChild(form);

		$H(this.data).each(function(el)
		{
			if (form.elements.namedItem(el[0]))
		 	{
				form.elements.namedItem(el[0]).value = el[1];
			}
		});

		form.elements.namedItem('id').value = this.data['ID'];

		form.elements.namedItem('text').id = 'tinyMce_text_' + this.data.ID;
		form.elements.namedItem('moreText').id = 'tinyMce_moreText_' + this.data.ID;

		// set up calendar field
		var time = this.node.down('#time');
		var time_real = this.node.down('#time_real');
		var time_button = this.node.down('#time_button');

		time_button.realInput = time_real;
		time_button.showInput = time;

		time.realInput = time_real;
		time.showInput = time;

		time_real.value = this.data['time'];

		Event.observe(time,		"keyup",	 Calendar.updateDate );
		Event.observe(time,		"blur",	  Calendar.updateDate );
		Event.observe(time_button, "mousedown", Calendar.updateDate );

		Calendar.setup({
			inputField:	 time,
			inputFieldReal: time_real,
			ifFormat:	   "%d-%b-%Y",
			button:		 time_button,
			align:		  "BR",
			singleClick:	true
		});

		if (window.tinyMCE)
		{
			tinyMCE.idCounter = 0;
		}

		ActiveForm.prototype.initTinyMceFields(this.node.down('div.formContainer'));

		form.down('a.cancel').onclick = this.cancelEditForm.bindAsEventListener(this);
		form.onsubmit = this.save.bindAsEventListener(this);

		new Backend.LanguageForm(form);

		this.list.toggleContainerOn(this.list.getContainer(this.node, 'edit'));
	},

	cancelEditForm: function(e)
	{
		if (!this.list.isContainerEmpty(this.node, 'edit'))
		{
			this.list.toggleContainerOff(this.list.getContainer(this.node, 'edit'));
		}

		var formContainer = this.node.down('div.formContainer');

		if (!formContainer.firstChild)
		{
			return;
		}

		ActiveForm.prototype.destroyTinyMceFields(formContainer);

		formContainer.innerHTML = '';

		if (e)
		{
			Event.stop(e);
		}
	},

	save: function(e)
	{
		Element.saveTinyMceFields(this.node);
		var form = this.node.down('form');
		form.action = $('saveUrl').innerHTML;
		new LiveCart.AjaxRequest(form, null, this.update.bind(this));
		Event.stop(e);
	},

	update: function(originalRequest)
	{
		this.data = originalRequest.responseData;
		this.updateHtml();
		this.cancelEditForm();
		Element.show(this.node.down('.checkbox'));
		ActiveList.prototype.highlight(this.node, 'yellow');
	},

	del: function()
	{

	},

	updateHtml: function()
	{
		if (1 == this.data.isEnabled)
		{
			Element.removeClassName(this.node, 'disabled');
		}
		else
		{
			Element.addClassName(this.node, 'disabled');
		}

		this.node.down('.newsTitle').innerHTML = this.data.title;

		if (this.data.formatted_time)
		{
			this.node.down('.newsDate').innerHTML = this.data.formatted_time.date_long;
		}

		this.node.down('.newsText').innerHTML = this.data.text;
		this.node.down('.checkbox').checked = (this.data.isEnabled == true);
		this.node.id = 'newsEntry_' + this.data.ID;
	},

	setEnabled: function(checkbox)
	{
		url = $('statusUrl').innerHTML + "?status=" + (checkbox.checked - 1 + 1) + '&id=' + this.list.getRecordId(this.node);
		Element.hide(checkbox);
		new LiveCart.AjaxRequest(url, this.node.down('.progressIndicator'), this.update.bind(this));
	}
}

Backend.SiteNews.Add = Class.create();
Backend.SiteNews.Add.prototype =
{
	form: null,

	initialize: function(form)
	{
		new LiveCart.AjaxRequest(form, null, this.onComplete.bind(this));
	},

	onComplete: function(originalRequest)
	{
		new Backend.SiteNews.PostEntry($('newsList'), $('newsList_template'), originalRequest.responseData, true, true);
		Backend.SiteNews.prototype.hideAddForm();
		$("newsForm").reset();
		ActiveForm.prototype.resetTinyMceFields($("newsForm"));
		//Form.State.restore($("newsForm"));
	}
}
