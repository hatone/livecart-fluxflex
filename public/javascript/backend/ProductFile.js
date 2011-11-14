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

if(!Backend) Backend = {};
if(!Backend.ProductFile) Backend.ProductFile = {};


/******************************************************************************
 * Product files
 * label:files
 *****************************************************************************/
Backend.ProductFile.Callbacks =
{
	beforeDelete: function(li){
		if(confirm(Backend.ProductFile.Messages.areYouSureYouWantToDelete))
		{
			return Backend.ProductFile.Links.deleteFile + "/" + this.getRecordId(li);
		}
	},
	afterDelete: function(li, response){
		try
		{
			response = eval('(' + response + ')');
		}
		catch(e)
		{
			return false;
		}

		if(!response.error) {
			var tabControl = TabControl.prototype.getInstance("productManagerContainer", false);
			tabControl.setCounter('tabProductFiles', tabControl.getCounter('tabProductFiles') - 1);
			return true;
		}

		return false;
	},
	beforeSort: function(li, order){
		return Backend.ProductFile.Links.sort + "?target=" + this.ul.id + "&" + order
	},
	afterSort: function(li, response){  },


	beforeEdit: function(li) {
		var container = this.getContainer(li, 'edit');
		if(this.isContainerEmpty(li, 'edit')) {
			var container  = this.getContainer(li, 'edit');
			new Insertion.Bottom(container, $('productFile_item_blank').innerHTML);
			container.down('.productFile_form').show();
			return Backend.ProductFile.Links.edit + "/" + this.getRecordId(li);
		}
		else {
			if(container.style.display != 'block')
			{
				this.toggleContainerOn(container);
				setTimeout(function() {
					ActiveForm.prototype.initTinyMceFields(li)
				}, 500);
			}
			else
			{
				this.toggleContainerOff(container);
				ActiveForm.prototype.destroyTinyMceFields(li);
			}
		}
	},

	afterEdit: function(li, response) {
		var model = new Backend.ProductFile.Model(eval("(" + response + ")"), Backend.availableLanguages);
		var controller = new Backend.ProductFile.Controller(li.down('.productFile_form'), model);

		this.toggleContainer(li, 'edit');

		setTimeout(function() {
			ActiveForm.prototype.initTinyMceFields(li);
		}, 500);
	}
}

Backend.ProductFile.Model = function(data, languages)
{
	this.store(data || {});

	if(!this.get('ID', false)) this.isNew = true;

	this.languages = $H(languages);
},

Backend.ProductFile.Model.methods = {
	save: function(serializedData, onSaveResponse)
	{
		if(true == this.saving) return;
		this.saving = true;
		this.serverError = false;

		var self = this;
		new LiveCart.AjaxRequest(Backend.ProductFile.Links.save,
		{
			method: 'post',
			postBody: serializedData,
			onSuccess: function(response)
			{
				var responseHash = {};
				try
				{
					responseHash = eval("(" + response.responseText + ")");
				}
				catch(e)
				{
					responseHash['status'] = 'serverError';
					responseHash['responseText'] = response.responseText;
				}

				self.afterSave(responseHash, onSaveResponse);
			}
		});
	},

	getDefaultLanguage: function()
	{
		if(this.defaultLanguage === false)
		{
			this.languages.each(function(language)
			{
				if(parseInt(language.value.isDefault))
				{
					this.defaultLanguage = language.value;
				}
			}.bind(this));
		}

		return this.defaultLanguage;
	}
};

Backend.ProductFile.Model.inheritsFrom(MVC.Model);

Backend.ProductFile.Controller = Class.create();
Backend.ProductFile.Controller.prototype = {
	instances: {},

	initialize: function(root, model)
	{
		this.model = model;
		this.view = new Backend.ProductFile.View(root, this.model.get('Product.ID'));

		if(!this.view.nodes.root.id) this.view.nodes.root.id = this.view.prefix + 'list_' + this.model.get('Product.ID') + '_' + this.model.get('ID') + '_form';

		var self = this;
		this.createUploadIFrame();
		this.setDefaultValues();

		if(!this.model.isNew) {
			this.createDownloadLink();
		}

		this.setTranslationValues();

		this.bindActions();

		Form.State.backup(this.view.nodes.form);

		Backend.ProductFile.Controller.prototype.instances[this.view.nodes.root.id] = this;
	},

	getInstance: function(rootNode)
	{
		return Backend.ProductFile.Controller.prototype.instances[$(rootNode).id];
	},

	createUploadIFrame: function()
	{
		var defaultLanguageID = this.model.getDefaultLanguage()['ID'];

		this.view.assign('defaultLanguageID', defaultLanguageID);
		this.view.assign('productID', this.model.get('Product.ID', ''));
		this.view.assign('ID', this.model.get('ID', ''));
		this.view.assign('controller', this);

		this.view.createUploadIFrame();

	},

	createDownloadLink: function()
	{
		this.view.assign('linkText', this.view.nodes.fileName.value + '.' + this.model.get('extension', 'ext'));
		this.view.assign('ID', this.model.get('ID', 0));

		this.view.createDownloadLink();
	},

	setDefaultValues: function()
	{
		var defaultLanguageID = this.model.getDefaultLanguage()['ID'];

		this.view.assign('defaultLanguageID', defaultLanguageID);
		this.view.assign('title', this.model.get('title_' + defaultLanguageID));
		this.view.assign('description', this.model.get('description_' + defaultLanguageID));
		this.view.assign('allowDownloadDays', this.model.get('allowDownloadDays'));
		this.view.assign('allowDownloadCount', this.model.get('allowDownloadCount'));
		this.view.assign('filePath', this.model.get('filePath'));
		this.view.assign('ID', this.model.get('ID', ''));
		this.view.assign('productID', this.model.get('Product.ID', ''));
		this.view.assign('isNew', this.model.isNew);
		this.view.assign('fileName', this.model.get('fileName'));
		this.view.assign('extension', this.model.get('extension'));
		this.view.assign('isEmbedded', this.model.get('isEmbedded'));
		this.view.assign('isPublic', this.model.get('isPublic'));

		this.view.setDefaultLanguageValues();
	},

	setTranslationValues: function()
	{
		var self = this;

		this.view.assign('defaultLanguageID', this.model.getDefaultLanguage()['ID']);
		var description = {};
		var title = {};
		this.model.languages.each(function(lang)
		{
		   description[lang.key] = self.model.get('description_' + lang.key);
		   title[lang.key] = self.model.get('title_' + lang.key);
		});

		this.view.assign('title', title);
		this.view.assign('description', description);
		this.view.assign('languages', this.model.languages);
		this.view.setOtherLanguagesValues(this.model);
	},

	bindActions: function()
	{
		var self = this;
		if(!this.model.isNew)
		{
			Event.observe(this.view.nodes.title, 'keyup', function(e) { self.onTitleChange(); });
		}
		Event.observe(this.view.nodes.form, 'submit', function(e) { ActiveForm.prototype.resetErrorMessages(self.view.nodes.root); });
		Event.observe(this.view.nodes.cancel, 'click', function(e) { Event.stop(e); self.onCancel(); });
		Event.observe(this.view.nodes.newFileCancelLink, 'click', function(e) { Event.stop(e); self.onCancel(); });
		Event.observe(this.view.nodes.save, 'click', function(e) { self.onSave(); });
	},

	onSave: function()
	{
		var url = this.model.isNew ? Backend.ProductFile.Links.create : Backend.ProductFile.Links.update;
		this.view.nodes.form.action = Backend.Router.setUrlQueryParam(url, "random", Math.random() * 100000);
		Element.saveTinyMceFields(this.view.nodes.form);
	},

	onCancel: function()
	{
		Form.State.restore(this.view.nodes.root);

		ActiveForm.prototype.resetErrorMessages(this.view.nodes.root);
		if(this.model.isNew)
		{
			this.hideNewForm();
			ActiveForm.prototype.resetTinyMceFields(this.view.nodes.root);
		}
		else
		{
			var activeList = ActiveList.prototype.getInstance(this.view.prefix + "list_" + this.model.get('Product.ID', '') + "_" + this.model.get('ProductFileGroup.ID', ''));
			activeList.toggleContainer(this.view.nodes.root.up('li'), 'edit');

			this.view.nodes.fileHeader.innerHTML = this.model.get('title');
		}
	},

	onTitleChange: function()
	{
		this.view.nodes.fileHeader.update(this.view.nodes.title.value);
	},

	onSaveResponse: function(status)
	{
		if('success' == status)
		{
			if(this.model.isNew)
			{
				this.view.assign('ID', this.model.get('ID'));
				this.view.assign('productID', this.model.get('Product.ID'));
				this.view.createNewFile();
				this.model.store('ID', false);
				this.hideNewForm();
				Form.State.restore(this.view.nodes.form);

				var tabControl = TabControl.prototype.getInstance("productManagerContainer", false);
				tabControl.setCounter('tabProductFiles', tabControl.getCounter('tabProductFiles') + 1);
			}
			else
			{
				this.view.nodes.fileHeader.update(this.view.nodes.title.value);
				this.createDownloadLink();

				var activeList = ActiveList.prototype.getInstance(this.view.prefix + "list_" + this.model.get('Product.ID', '') + "_" + this.model.get('ProductFileGroup.ID', ''));
				activeList.toggleContainer(this.view.nodes.root.up("li"), 'edit', 'yellow');
			}
			Form.State.restore(this.view.nodes.root);
		}
		else
		{
			ActiveForm.prototype.setErrorMessages(this.view.nodes.root, this.model.errors);
		}
	},

	hideNewForm: function()
	{
		var menu = new ActiveForm.Slide(this.view.prefix + "menu_" + this.model.get('Product.ID'));
		menu.hide(this.view.prefix + "add", this.view.nodes.root);
	},

	showNewForm: function()
	{
		var menu = new ActiveForm.Slide(this.view.prefix + "menu_" + this.model.get('Product.ID'));
		menu.show(this.view.prefix + "add", this.view.nodes.root);
	}
}

Backend.ProductFile.View = function(root, productID)
{
	this.findNodes(root, productID);
	this.clear();
}

Backend.ProductFile.View.methods =
{
	prefix: 'productFile_',

	findNodes: function(root, productID)
	{
		this.nodes = {};
		this.nodes.root = root;

		this.nodes.form = ('FORM' == this.nodes.root.tagName) ? this.nodes.root : this.nodes.root.down('form');

		// controls
		this.nodes.controls = this.nodes.root.down('.' + this.prefix + 'controls');
		this.nodes.save = this.nodes.controls.down('.' + this.prefix + 'save');
		this.nodes.cancel = this.nodes.controls.down('.' + this.prefix + 'cancel');

		this.nodes.id = this.nodes.root.down('.' + this.prefix + 'ID');
		this.nodes.productID = this.nodes.root.down('.' + this.prefix + 'productID');
		this.nodes.description = this.nodes.root.down('.' + this.prefix + 'description');
		this.nodes.title = this.nodes.root.down('.' + this.prefix + 'title');
		this.nodes.allowDownloadDays = this.nodes.root.down('.' + this.prefix + 'allowDownloadDays');
		this.nodes.allowDownloadCount = this.nodes.root.down('.' + this.prefix + 'allowDownloadCount');
		this.nodes.filePath = this.nodes.root.down('.' + this.prefix + 'filePath');
		this.nodes.uploadFile = this.nodes.root.down('.' + this.prefix + 'uploadFile');
		this.nodes.isEmbedded = this.nodes.root.down('.' + this.prefix + 'isEmbedded');
		this.nodes.isPublic = this.nodes.root.down('.' + this.prefix + 'isPublic');

		this.nodes.extension = this.nodes.root.down('.' + this.prefix + 'extension');

		if(this.nodes.root.up('li'))
		{
			this.nodes.fileHeader = this.nodes.root.up('li').down('.' + this.prefix + 'item_title');
		}

		this.nodes.fileName = this.nodes.root.down('.' + this.prefix + 'fileName');
		this.nodes.fileNameBlock = this.nodes.root.down('.' + this.prefix + 'fileName_div');

		this.nodes.downloadLink = this.nodes.root.down('.' + this.prefix + 'download_link');

		this.nodes.newFileCancelLink = $(this.prefix + 'new_' + productID + '_cancel');

		['isEmbedded', 'isPublic'].each(function(f)
		{
			this.nodes[f].id = this.nodes[f].className + '_' + Math.random();
			this.nodes[f].parentNode.down('label').setAttribute('for', this.nodes[f].id);
		}.bind(this));

		new Backend.LanguageForm(root);
	},

	createUploadIFrame: function()
	{
		var iframe = document.createElement('iframe');
		iframe.hide();
		iframe.name = iframe.id = "productFileUploadIFrame_" + this.get("productID", '') + "_" + this.get("ID", '');
		this.nodes.root.appendChild(iframe);
		this.nodes.form.target = iframe.name;

		this.nodes.iframe = iframe;

		var controller = this.get('controller', null);
		this.nodes.iframe.controller = controller;
		this.nodes.iframe.action = function(status) { controller.onSaveResponse(status) };
	},

	createDownloadLink: function()
	{
		this.nodes.downloadLink.href = Backend.ProductFile.Links.download + "/" + this.get('ID');
		this.nodes.downloadLink.update(this.get('linkText'));
		this.nodes.downloadLink.show();
		this.clear();
	},

	setDefaultLanguageValues: function()
	{
		this.nodes.id.value = this.get('ID', '');
		this.nodes.productID.value = this.get('productID', '');

		if(this.get('isNew'))
		{
		   this.nodes.fileNameBlock.hide();
		}
		else
		{
			this.nodes.fileNameBlock.show();
			this.nodes.fileName.value = this.get('fileName');
			this.nodes.filePath.value = this.get('filePath');
			this.nodes.extension.update('.' + this.get('extension'));
		}

		this.nodes.description.name += '_' + this.get('defaultLanguageID');
		this.nodes.description.value = this.get('description', '');

		this.nodes.title.name += '_' + this.get('defaultLanguageID');
		this.nodes.title.value = this.get('title', '');

		this.nodes.allowDownloadDays.value = this.get('allowDownloadDays', 0);
		this.nodes.allowDownloadCount.value = this.get('allowDownloadCount', 0);
		this.nodes.isEmbedded.checked = this.get('isEmbedded', 0) == "1";
		this.nodes.isPublic.checked = this.get('isPublic', 0) == "1";

		this.nodes.form.action += "/" + this.get('ID', '');

		this.clear();
	},

	setOtherLanguagesValues: function()
	{
		var defaultLanguageID = this.get('defaultLanguageID');

		var self = this;
		var languages = this.get('languages', {});
		languages.each(function(language)
		{
			if(language.value.ID == defaultLanguageID) return;

			self.nodes.form.elements.namedItem('description_' + language.key).value = self.get('description.' + language.key , 'no');
			self.nodes.form.elements.namedItem('title_' + language.key).value = self.get('title.' + language.key , '');
		});

		this.clear();
	},

	createNewFile: function()
	{
		var activeList = ActiveList.prototype.getInstance(this.prefix + 'list_' + this.get('productID') + '_');

		var fileContainer = document.createElement('div');
		fileContainer.update('<span class="' + this.prefix + 'item_title">' + this.nodes.title.value + '</span>');

		var li = activeList.addRecord(this.get('ID'), fileContainer)
		Element.addClassName(li, 'productFile_item');

		this.clear();
	},

	showForm: function()
	{
		var li = this.nodes.root.up("li");
		var activeList = ActiveList.prototype.getInstance(li.up('ul'));

		ActiveList.prototype.collapseAll();
		this.nodes.title.hide();
		activeList.toggleContainerOn(li.down('.' + this.prefix + 'form'));

		this.clear();
	},

	hideForm: function(highlight)
	{
		var li = this.nodes.root.up("li");
		var activeList = ActiveList.prototype.getInstance(li.up('ul'));

		this.nodes.title.show();
		activeList.toggleContainer(li, 'edit', highlight);

		this.clear();
	}
}

Backend.ProductFile.View.inheritsFrom(MVC.View);

/******************************************************************************
 * Product files group
 * label:group
 *****************************************************************************/
Backend.ProductFile.Group = {};
Backend.ProductFile.Group.Callbacks =
{
	beforeDelete: function(li) {
		if(confirm(Backend.ProductFile.Group.Messages.areYouSureYouWantToDelete))
		{
			return Backend.ProductFile.Group.Links.remove + "/" + this.getRecordId(li);
		}
	},
	afterDelete: function(li, response) {
		try
		{
			response = eval('(' + response + ')');
		}
		catch(e)
		{
			return false;
		}

		if(!response.error) {
			var tabControl = TabControl.prototype.getInstance("productManagerContainer", false);
			tabControl.setCounter('tabProductFiles', tabControl.getCounter('tabProductFiles') - li.getElementsByTagName('li').length + 2);
			return true;
		}

		return false;
	},
	beforeSort: function(li, order) {
		return Backend.ProductFile.Group.Links.sort + '&' + order;
	},
	afterSort: function(li, response) {

	},

	beforeEdit:	 function(li)
	{
		if(!Backend.ProductFile.Group.Controller.prototype.getInstance(li.down('.productFileGroup_form')))
		{
			return Backend.ProductFile.Group.Links.edit + "/" + this.getRecordId(li);
		}
		else
		{
			var object = Backend.ProductFile.Group.Controller.prototype.getInstance(li.down('.productFileGroup_form'));

			if(this.getContainer(li, 'edit').style.display != 'block') object.showForm();
			else object.hideForm();
		}
	},
	afterEdit:	  function(li, response)
	{
		response = eval("(" + response + ")");

		var model = new Backend.ProductFile.Group.Model(response, Backend.availableLanguages);
		var group = new Backend.ProductFile.Group.Controller(li.down('.productFileGroup_form'), model);
		group.showForm();
	}
}


Backend.ProductFile.Group.Model = function(data, languages)
{
	this.store(data || {});

	if(!this.get('ID', false)) this.isNew = true;

	this.languages = $H(languages);
}

Backend.ProductFile.Group.Model.methods = {
	defaultLanguage: false,

	save: function(form, onSaveResponse)
	{
		if(true == this.saving) return;
		this.saving = true;
		this.serverError = false;

		var self = this;

		form.action = this.isNew ? Backend.ProductFile.Group.Links.create : Backend.ProductFile.Group.Links.update;

		new LiveCart.AjaxRequest(
			form,
			false,
			function(response)
			{
				var responseHash = {};
				try
				{
					responseHash = eval("(" + response.responseText + ")");
				}
				catch(e)
				{
					responseHash['status'] = 'serverError';
					responseHash['responseText'] = response.responseText;
				}

				self.afterSave(responseHash, onSaveResponse, form);
			}
		);
	},

	afterSave: function(response, onSaveResponse, form)
	{
		switch(response.status)
		{
			case 'success':
				this.store('ID', response.ID);
				break;
			case 'failure':
				this.errors = response.errors;
				break;
			case 'serverError':
				this.serverError = response.responseText;
				break;
		}

		onSaveResponse.call(this, response.status);
		this.saving = false;
	},

	getDefaultLanguage: function()
	{
		if(this.defaultLanguage === false)
		{
			this.languages.each(function(language)
			{
				if(parseInt(language.value.isDefault))
				{
					this.defaultLanguage = language.value;
				}
			}.bind(this));
		}

		return this.defaultLanguage;
	}
}

Backend.ProductFile.Group.Model.inheritsFrom(MVC.Model);

Backend.ProductFile.Group.Controller = Class.create();
Backend.ProductFile.Group.Controller.prototype = {
	instances: {},

	initialize: function(root, model)
	{
		this.model = model;
		this.view = new Backend.ProductFile.Group.View(root, this.model.get('Product.ID'));

		if(!this.view.nodes.root.id) this.view.nodes.root.id = this.view.prefix + 'list_' + this.model.get('Product.ID') + '_' + this.model.get('ID', '') + '_form';

		this.setDefaultValues();
		this.setTranslationValues();

		this.bindActions();

		Form.State.backup(this.view.nodes.root);

		Backend.ProductFile.Group.Controller.prototype.instances[this.view.nodes.root.id] = this;

	},

	getInstance: function(rootNode)
	{
		return Backend.ProductFile.Group.Controller.prototype.instances[$(rootNode).id];
	},

	setDefaultValues: function()
	{
		var defaultLanguageID = this.model.getDefaultLanguage()['ID'];

		this.view.assign('defaultLanguageID', defaultLanguageID);
		this.view.assign('name', this.model.get('name_' + defaultLanguageID));
		this.view.assign('ID', this.model.get('ID', ''));
		this.view.assign('productID', this.model.get('Product.ID', ''));

		this.view.setDefaultLanguageValues();
	},

	setTranslationValues: function()
	{
		var self = this;

		this.view.assign('defaultLanguageID', this.model.getDefaultLanguage()['ID']);
		var name = {};
		this.model.languages.each(function(lang)
		{
		   name[lang.key] = self.model.get('name_' + lang.key)
		});

		this.view.assign('name', name);
		this.view.assign('languages', this.model.languages);
		this.view.setOtherLanguagesValues(this.model);
	},

	bindActions: function()
	{
		var self = this;

		Event.observe(this.view.nodes.save, 'click', function(e) { Event.stop(e); self.onSave(); });
		Event.observe(this.view.nodes.cancel, 'click', function(e) { Event.stop(e); self.onCancel(); });
		Event.observe(this.view.nodes.newGroupCancelLink, 'click', function(e) { Event.stop(e); self.onCancel(); });
		Event.observe(this.view.nodes.name, 'keyup', function(e) { Event.stop(e); self.onNameChange(); });

	},

	onSave: function()
	{
		var self = this;
		ActiveForm.prototype.resetErrorMessages(this.view.nodes.root);
		this.model.save(this.view.nodes.root.down('form'), function(status) {
			self.onSaveResponse(status) ;
		});
	},

	onNameChange: function()
	{
		if(!this.model.isNew) this.view.nodes.title.update(this.view.nodes.name.value);
	},

	onCancel: function()
	{
		Form.State.restore(this.view.nodes.root);
		ActiveForm.prototype.resetErrorMessages(this.view.nodes.root);

		if(this.model.isNew)
		{
			this.hideNewForm();
		}
		else
		{
			this.hideForm();
		}
	},

	onSaveResponse: function(status)
	{
		if('success' == status)
		{
			if(this.model.isNew)
			{
				this.view.assign('ID', this.model.get('ID'));
				this.view.assign('productID', this.model.get('Product.ID'));
				this.view.createNewGroup();
				this.model.store('ID', false);

				this.hideNewForm();
				this.view.nodes.root.down('form').reset();
			}
			else
			{
				this.view.nodes.title.update(this.view.nodes.name.value);
				this.hideForm('yellow');
			}

			Form.State.backup(this.view.nodes.root);
		}
		else
		{
			ActiveForm.prototype.setErrorMessages(this.view.nodes.root, this.model.errors);
		}
	},

	hideNewForm: function()
	{
		var menu = new ActiveForm.Slide("productFile_menu_" + this.model.get('Product.ID'));
		menu.hide(this.view.prefix + "add", this.view.nodes.root);
	},

	showNewForm: function()
	{
		var menu = new ActiveForm.Slide("productFile_menu_" + this.model.get('Product.ID'));
		menu.show(this.view.prefix + "add", this.view.nodes.root);
	},

	showForm: function()
	{
		this.view.showForm();
	},

	hideForm: function(highlight)
	{
		this.view.nodes.title.update(this.view.nodes.name.value);
		this.view.hideForm(highlight);

	}

}


Backend.ProductFile.Group.View = function(root, productID)
{
	this.findNodes(root, productID);
	this.clear();
}

Backend.ProductFile.Group.View.methods = {
	prefix: 'productFileGroup_',

	findNodes: function(root, productID)
	{
		this.nodes = {};
		this.nodes.root = root;
		this.nodes.form = ('FORM' == this.nodes.root.tagName) ? this.nodes.root : this.nodes.root.down('form');

		// controls
		this.nodes.controls = this.nodes.root.down('.' + this.prefix + 'controls');
		this.nodes.save = this.nodes.controls.down('.' + this.prefix + 'save');
		this.nodes.cancel = this.nodes.controls.down('.' + this.prefix + 'cancel');

		this.nodes.id = this.nodes.root.down('.' + this.prefix + 'ID');
		this.nodes.productID = this.nodes.root.down('.' + this.prefix + 'productID');
		this.nodes.name = this.nodes.root.down('.' + this.prefix + 'name');

		if(this.nodes.root.up('li')) this.nodes.title = this.nodes.root.up('li').down('.' + this.prefix + 'title');

		this.nodes.newGroupCancelLink = $(this.prefix + 'new_' + productID + '_cancel');

		this.nodes.languageForm = this.nodes.root.down('.languageForm');

		new Backend.LanguageForm(this.nodes.languageForm);
	},

	setDefaultLanguageValues: function()
	{
		this.nodes.id.value = this.get('ID', '');
		this.nodes.productID.value = this.get('productID', '');

		this.nodes.name.name += '_' + this.get('defaultLanguageID');
		this.nodes.name.value = this.get('name', '');

		this.clear();
	},

	setOtherLanguagesValues: function()
	{
		var defaultLanguageID = this.get('defaultLanguageID');

		var self = this;
		var languages = this.get('languages', {});
		languages.each(function(language)
		{
			if(language.value.ID == defaultLanguageID) return;
			self.nodes.form.elements.namedItem('name_' + language.key).value = self.get('name.' + language.key, '');
		});

		this.clear();
	},

	createNewGroup: function()
	{
		var activeList = ActiveList.prototype.getInstance($(this.prefix + "list_" + this.get('productID')), Backend.ProductFile.Group.Callbacks, Backend.ProductFile.Group.Messages);

		var containerDiv = document.createElement('div');
		containerDiv.update(
			'<span class="' + this.prefix + 'title">' + this.nodes.name.value + '</span>'
			+ $('productFileGroup_item_blank').innerHTML
			+ '<ul id="productFile_list_' + this.get('productID') + '_' + this.get('ID') + '" class="productFile_list activeList_add_sort activeList_add_edit activeList_add_delete activeList_accept_productFile_list">'
			+ '</ul>'
		);

		var li = activeList.addRecord(this.get('ID'), containerDiv);
		Element.addClassName(li, this.prefix  + 'item');

		var newGroupProductsList = ActiveList.prototype.getInstance(li.down('.productFile_list'), Backend.ProductFile.Callbacks);
		ActiveList.prototype.recreateVisibleLists();

		activeList.touch(true)

		this.clear();
	},

	showForm: function()
	{
		var li = this.nodes.root.up("li");
		var activeList = ActiveList.prototype.getInstance(li.up('ul'));
		li.down('.' + this.prefix + 'form').style.display = 'block';
		ActiveList.prototype.collapseAll();

		activeList.toggleContainerOn(activeList.getContainer(li, 'edit'));

		this.clear();
	},

	hideForm: function(highlight)
	{
		var li = this.nodes.root.up("li");
		var activeList = ActiveList.prototype.getInstance(li.up('ul'));

		activeList.toggleContainerOff(activeList.getContainer(li, 'edit', highlight));

		this.clear();
	}

}

Backend.ProductFile.Group.View.inheritsFrom(MVC.View);

Backend.RegisterMVC(Backend.ProductFile);
Backend.RegisterMVC(Backend.ProductFile.Group);