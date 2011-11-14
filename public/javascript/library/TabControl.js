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

Event.fire = function(element, event)
{
   Event.observers.each(function(observer)
   {
		if(observer[1] == event && observer[0] == element)
		{
			var func = observer[2];
			func();
		}
   });
};


var TabControl = Class.create();
TabControl.prototype = {
	__instances__: {},
	activeTab: null,
	indicatorImageName: "image/indicator.gif",

	initialize: function(tabContainerName, urlParserCallback, idParserCallback, callbacks)
	{
		this.tabContainerName = tabContainerName;
		this.urlParserCallback = urlParserCallback || this.getTabUrl;
		this.idParserCallback = idParserCallback || this.getContentTabId;
		this.callbacks = callbacks ? callbacks : {};
		this.loadedContents = {};

		this.__nodes__();
		this.__bind__();

		this.decorateTabs();
		this.countersCache = {};
	},

	__nodes__: function()
	{
		this.nodes = {};
		this.nodes.tabContainer = $(this.tabContainerName);
		this.nodes.tabList = this.nodes.tabContainer.down(".tabList");
		this.nodes.tabListElements = document.getElementsByClassName("tab", this.nodes.tabList);
		this.nodes.sectionContainer = this.nodes.tabContainer.down(".sectionContainer");
		this.nodes.notabsContainer = this.nodes.tabContainer.down(".notabsContainer");
	},

	__bind__: function()
	{
		var self = this;
		this.nodes.tabListElements.each(function(li)
		{
			var link = li.down('a');
			var indicator = '<img src="' + self.indicatorImageName + '" class="tabIndicator" alt="Tab indicator" style="display: none" /> ';

			if(link.hasClassName("observed") == false)
			{
				Event.observe(link, 'click', function(e) { if(e) Event.stop(e); });
				link.addClassName("observed");
			}

			if(li.hasClassName("observed"))
			{
				return;
			}
			link.addClassName("li");
			li.onclick = function(e) {
				if(!e) e = window.event;
				if(e) Event.stop(e);

				if (e)
				{
					var keyboard = new KeyboardEvent(e);
					if (keyboard.isShift())
					{
						self.resetContent(Event.element(e).up('li'));
					}
				}

				self.handleTabClick({'target': li});

				if(Backend.Produc)
				{
					Backend.Product.hideAddForm();
				}
			}

			Event.observe(li, 'mouseover', function(e) {
				if(e) Event.stop(e);
				self.handleTabMouseOver({'target': li})
			});
			Event.observe(li, 'mouseout', function(e) {
				if(e) Event.stop(e);
				self.handleTabMouseOut({'target': li})
			});

			li.update(indicator + li.innerHTML);
		});
	},

	decorateTabs: function()
	{
		this.nodes.tabListElements.each(function(tab)
		{
			if (!tab.down('.tabCounter'))
			{
				var firstLink = tab.down('a');
				new Insertion.After(firstLink, '<span class="tabCounter"> </span>');
			}
		});
	},

	getInstance: function(tabContainerName, urlParserCallback, idParserCallback, callbacks)
	{
		if(!TabControl.prototype.__instances__[tabContainerName])
		{
			TabControl.prototype.__instances__[tabContainerName] = new TabControl(tabContainerName, urlParserCallback, idParserCallback, callbacks);
		}

		return TabControl.prototype.__instances__[tabContainerName];
	},

	handleTabMouseOver: function(args)
	{
		if (this.activeTab != args.target)
		{
			Element.removeClassName(args.target, 'inactive');
			Element.addClassName(args.target, 'hover');
		}
	},

	handleTabMouseOut: function(args)
	{
		if (this.activeTab != args.target)
		{
			Element.removeClassName(args.target, 'hover');
			Element.addClassName(args.target, 'inactive');
		}
	},

	handleTabClick: function(args)
	{
		// hide all error messages within a background process (to avoid potential tab switching delays)
		window.setTimeout(function()
			{
				document.getElementsByClassName('redMessage').each(function(message){ message.hide(); });
				document.getElementsByClassName('bugMessage').each(function(message){ message.hide(); });
			}, 10);

		if(this.callbacks.beforeClick) this.callbacks.beforeClick.call(this);
		this.activateTab(args.target);
		if(this.callbacks.afterClick) this.callbacks.afterClick.call(this);
	},

	addHistory: function()
	{
		setTimeout(function()
		{
			var locationHash = "#" + Backend.ajaxNav.getHash();
			var updateHistory = false;
			this.nodes.tabListElements.each(function(tab)
			{
				if(locationHash.indexOf("#" + tab.id) !== -1)
				{
					locationHash = locationHash.substring(0, locationHash.indexOf(tab.id) - 1);
					updateHistory = true;
					throw $break;
				}
			});

			if(locationHash.match(/__/))
			{
				locationHash = locationHash.substr(0, locationHash.length - 2);
			}

			Backend.ajaxNav.add(locationHash.substring(1) + "#" + this.activeTab.id);
		}.bind(this), dhtmlHistory.currentWaitTime * 2);
	},

	/**
	 * Reloades tab content in background (does not activate tab)
	 * 
	 * When reloading:
	 *              Existing tab content container is removed
	 *              If not active tab, content container is set to absolute position and positioned
	 *                                 outside page, because some controls can't initalize in invisible
	 *                                 container (for example editArea)
	 *              AjaxUpdater request is executed.
	 * 
	 * If tab content is not yet loaded, this will load tab content.
	 * 
	 */
	reloadTabContent: function(targetTab, onComplete)
	{
		targetTab = $(targetTab);
		if(!targetTab)
		{
			targetTab = this.nodes.tabListElements[0];
		}
		var contentId = this.idParserCallback(targetTab.id);
		if(!contentId)
		{
			return;
		}
		this.resetContent(targetTab);
		if ($(contentId))
		{
			$(contentId).remove();
		}
		new Insertion.Top(this.nodes.sectionContainer, '<div id="' + contentId + '" class="tabPageContainer ' + targetTab.id + 'Content loadedInBackgroundTab"></div>');
		this.activeContent = $(contentId);
		
		if (this.activeTab.id == targetTab.id )
		{
			this.activeContent.removeClassName("loadedInBackgroundTab");
		}
		if(!onComplete && this.callbacks.onComplete)
		{
			onComplete = this.callbacks.onComplete;
		}
		this.loadedContents[this.urlParserCallback(targetTab.down('a').href) + contentId] = true;
		new LiveCart.AjaxUpdater(
			this.urlParserCallback(targetTab.down('a').href),
			contentId, 
			targetTab.down('.tabIndicator'),
			'bottom',
			function(activeContent, onComplete, response)
			{
				if (onComplete)
				{
					onComplete(response);
				}
			}.bind(this, this.activeContent, onComplete)
		);
	},

	activateTab: function(targetTab, onComplete)
	{
		targetTab = $(targetTab);

		if(!targetTab)
		{
			targetTab = this.nodes.tabListElements[0];
		}

		// get help context
		var helpContext = document.getElementsByClassName('tabHelp', targetTab);
		if ((helpContext.length > 0) && helpContext[0].firstChild)
		{
			Backend.setHelpContext(helpContext[0].firstChild.nodeValue);
		}

		var contentId = this.idParserCallback(targetTab.id);

		// Cancel loading tab if false was returned
		if(!contentId)
		{
			return;
		}

		if(!$(contentId)) new Insertion.Top(this.nodes.sectionContainer, '<div id="' + contentId + '" class="tabPageContainer ' + targetTab.id + 'Content"></div>');

		var self = this;
		$A(this.nodes.tabListElements).each(function(tab) {
			if (tab.hasClassName('tab'))
			{
				Element.removeClassName(tab, 'active');
				Element.addClassName(tab, 'inactive');
			}
		});

		document.getElementsByClassName("tabPageContainer", this.nodes.sectionContainer).each(function(container) {
			Element.hide(container);
		})

		this.activeTab = targetTab;
		this.activeContent = $(contentId);
		this.activeContent.removeClassName("loadedInBackgroundTab");

		Element.removeClassName(this.activeTab, 'hover');
		Element.addClassName(this.activeTab, 'active');
		Element.show(contentId);

		if(!onComplete && this.callbacks.onComplete)
		{
			onComplete = this.callbacks.onComplete;
		}

		if (!this.loadedContents[this.urlParserCallback(targetTab.down('a').href) + contentId] && Element.empty($(contentId)))
		{
			this.loadedContents[this.urlParserCallback(targetTab.down('a').href) + contentId] = true;
			new LiveCart.AjaxUpdater(this.urlParserCallback(targetTab.down('a').href), contentId, targetTab.down('.tabIndicator'), 'bottom', function(activeContent, onComplete, response)
			{
				if (Form.focus)
				{
					setTimeout(function() { Form.focus(activeContent) }.bind(this, activeContent), 20);
				}

				if (onComplete)
				{
					onComplete(response);
				}

			}.bind(this, this.activeContent, onComplete));
			
			
		}
		else if(onComplete)
		{
			if (Form.focus)
			{
				Form.focus(this.activeContent);
			}

			onComplete();
		}
		else
		{
			if (Form.focus)
			{
				Form.focus(this.activeContent);
			}
		}

		this.addHistory();

		// Hide tabs if only one is visible
		this.nodes.tabList.show();
		this.hideAllTabs = true;
		this.nodes.tabListElements.each(function(tab) {
			if(targetTab != tab && tab.style.display != 'none')
			{
				this.hideAllTabs = false;
				throw $break;
			}
		}.bind(this));

		if(this.hideAllTabs)
		{
			this.nodes.tabList.hide();
		}
	},

	/**
	 * Reset content related to a given tab. When tab will be activated content must
	 * be resent
	 */
	resetContent: function(tabObj)
	{
		if (!tabObj)
		{
			return false;
		}

		var id = this.idParserCallback(tabObj.id);
		this.loadedContents[this.urlParserCallback(tabObj.down('a').href) + id] = false;
		if ($(id))
		{
			$(id).update();
		}
	},

	reloadActiveTab: function()
	{
		this.resetContent(this.activeTab);
		this.activateTab(this.activeTab);
	},

	getActiveTab: function()
	{
		return this.activeTab;
	},

	setTabUrl: function(tabId, url)
	{
		$(tabId).url = url;
	},

	setCounter: function(tab, value, hashId)
	{
		if(!this.countersCache[hashId]) this.countersCache[hashId] = {};

		tab = $(tab);

		if(!tab) return; //throw new Error('Could not find tab!');

		var counter = tab.down('.tabCounter');
		if(false === value)
		{
			counter.update('');
			delete this.countersCache[hashId][tab.id];
		}
		else
		{
			if (null == value)
			{
				value = 0;
			}

			counter.update("(" + value + ")");
			this.countersCache[hashId][tab.id] = value;
		}
	},

	setAllCounters: function(counters, hashId)
	{
		var self = this;
		$H(counters).each(function(tab) {
			self.setCounter(tab[0], tab[1], hashId);
		});
	},

	restoreCounter: function(tab, hashId)
	{
		tab = $(tab);

		if(tab && this.countersCache[hashId][tab.id] !== undefined)
		{
			this.setCounter(tab.id, this.countersCache[hashId][tab.id]);
			return true;
		}

		return false;
	},

	restoreAllCounters: function(hashId)
	{
		var restored = false;
		if(this.countersCache[hashId])
		{
			$A(this.nodes.tabListElements).each(function(tab) {
				restored = this.restoreCounter(tab, hashId) ? true : restored;
			}.bind(this));
		}

		return restored;
	},

	getCounter: function(tab)
	{
		tab = $(tab);

		if(!tab) throw new Error('Could not find tab!');

		var counter = tab.down('.tabCounter');
		var match = counter.innerHTML.match(/\((\d+)\)/);
		return match ? parseInt(match[1]) : 0;
	},

	getTabUrl: function(url)
	{
		return url;
	},

	getContentTabId: function(id)
	{
		return id + 'Content';
	},

	addNewTab: function(title)
	{
		var
			id = ["tab",new Date().getTime(), Math.round(Math.random() * 100000000)].join(""),
			li = document.createElement('li'),
			div = document.createElement('div');

		this.nodes.tabList.appendChild(li);
		li.innerHTML = '<a href="#" class="li">'+title+'</a>';
		li.addClassName("tab inactive");
		this.nodes.sectionContainer.appendChild(div);
		div.id=this.getContentTabId(id);
		div.addClassName("tabPageContainer");
		li.id = id;
		this.__nodes__();
		this.__bind__();
		this.decorateTabs();
		this.activateTab(id);
		if(this.nodes.notabsContainer)
		{
			this.nodes.sectionContainer.show();
			this.nodes.notabsContainer.hide();
		}
		return id;
	},

	setTitle: function(id, title)
	{
		$(id).down("a").innerHTML = title;
	},

	removeTab: function(id)
	{
		var
			tab = $(id),
			content = $(this.getContentTabId(id)),
			newActiveTab = tab.previous();

		tab.parentNode.removeChild(tab);
		content.parentNode.removeChild(content);
		this.__nodes__();
		if(this.nodes.tabListElements.length == 0 && this.nodes.notabsContainer)
		{
			this.nodes.sectionContainer.hide();
			this.nodes.notabsContainer.show();
			return null;
		}
		else
		{
			this.activateTab(newActiveTab);
			return this.getActiveTab();
		}
	}
}
