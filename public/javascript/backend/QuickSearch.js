/*************************************************************************************************
 * LiveCart																					  *
 * Copyright (C) 2007-2009 UAB "Integry Systems" (http://livecart.com)							*
 * All rights reserved																		   *
 *																							   *
 * This source file is a part of LiveCart software package and is protected by LiveCart license. *
 * The license text can be found in the license.txt file. In case you received a package without *
 * a license file, the license text is also available online at http://livecart.com/license	  *
 *************************************************************************************************//**
 *
 * @author Integry Systems
 */

window.quickSearchInstances = {};

Backend.QuickSearch = Class.create();
Backend.QuickSearch.prototype = {
	// timeout values
	TIMEOUT_WHEN_WAITING : 500,
	TIMEOUT_WHEN_TYPING : 1000,

	// --
	query : "",
	previousQuery: null,
	timer : {typing: null, waiting:null},
	popupHideObserved: false,
	nodes : null,
	hasResponse : false,
	prefix: null,
	defaultOptions:{},

	initialize: function(prefix, options)
	{
		if(options)
		{
			this.defaultOptions = options;
		}
		this.prefix = prefix;
		this.initNodes();
		Event.observe(this.nodes.Query, "keyup", this.onKeyUp.bindAsEventListener(this));
		Event.observe(this.nodes.Query, 'focus', this.onFocus.bindAsEventListener(this));
		Event.observe(this.nodes.Query, 'blur', this.onBlur.bindAsEventListener(this));
	},

	initNodes : function()
	{
		if(this.nodes == null)
		{
			this.nodes = {};
			$A([
				"Class",
				"From",
				"To",
				"Direction",
				"Form",
				"Query",
				"Result",
				"Container"
			]).each(
				function(id)
				{
					this.nodes[id]=$(this.prefix + id);
				}.bind(this)
			);

			this.nodes.Result = this.nodes.Result.parentNode;
		}
	},

	onFocus: function(event)
	{
		var obj = Event.element(event);
		if (obj.hint == undefined)
		{
			obj.hint = obj.value;
		}

		if (obj.value == obj.hint)
		{
			obj.value = '';
			obj.removeClassName('hasHint');
		}
	},

	onBlur: function(event)
	{
		var obj = Event.element(event);

		if (!obj.value)
		{
			obj.value = obj.hint;
			obj.addClassName('hasHint');
		}
	},

	onKeyUp:function(event)
	{
		var obj = Event.element(event);
		this.query=obj.value;

		// instant requests
		if(this.TIMEOUT_WHEN_TYPING == 0)
		{
			this.doRequest();
			return;
		}

		// When stop typing make request after TIMEOUT_WHEN_WAITING.
		if(this.timer.waiting)
		{
			window.clearTimeout(this.timer.waiting);
		}
		this.timer.waiting = window.setTimeout(this.doRequest.bind(this), this.TIMEOUT_WHEN_WAITING);

		// When typing make requests every TIMEOUT_WHEN_TYPING.
		if(this.timer.typing == null)
		{
			// using setTimeout instead of setInterval allows to restart interval
			// and make request every time user start typing after pause.
			this.timer.typing = window.setTimeout(
				function()
				{
					if(this.timer.typing)
					{
						window.clearTimeout(this.timer.typing);
						this.timer.typing = null;
					}
				}.bind(this),
				this.TIMEOUT_WHEN_TYPING
			);
			this.doRequest();
		}
	},

	doRequest: function(t)
	{
		if(this.query.length < 3)
		{
			return;
		}

		this.initNodes(); // move to 'constructor'

		if(this.query != this.previousQuery)
		{
			this._setFormOptions(this.defaultOptions);
			new LiveCart.AjaxRequest(
				this.nodes.Form,
				this.nodes.Query,
				this.onResponse.bind(this)
			);
			this.previousQuery = this.query;
		}
	},

	onResponse: function(transport)
	{
		this.initNodes(); // move to 'constructor'
		this.nodes.Result.innerHTML = transport.responseText;
		this.hasResponse = true; // flag needed to allow reopen closed result popup when clicking on search query input field.
		this.showResultContainer();
	},

	showResultContainer: function()
	{
		if(this.hasResponse == false)
		{
			return;
		}
		this.initNodes();
		this.nodes.Result.show();
		if(this.popupHideObserved == false)
		{
			Event.observe(document, 'click', this.hideResultContainer.bindAsEventListener(this));
			Event.observe(this.nodes.Container, 'click',
				function(event)
				{
					var element = Event.element(event);
					if(element.tagName.toLowerCase() != "a" || element.hasClassName("qsNext") || element.hasClassName("qsPrevious"))
					{
						Event.stop(event);
					}
				}
			);
			Event.observe(this.nodes.Query, 'focus', this.showResultContainer.bindAsEventListener(this));
			this.popupHideObserved = true;
		}

		if (this.callbacks && this.callbacks.showResultContainer && typeof this.callbacks.showResultContainer == "function")
		{
			this.callbacks.showResultContainer(this);
		}
	},

	onShowResultContainer: function(callback)
	{
		if (typeof this.callbacks == "undefined")
		{
			this.callbacks = {};
		}
		this.callbacks.showResultContainer = callback;
	},

	hideResultContainer: function()
	{
		this.initNodes();
		this.nodes.Result.hide();
	},

	next: function(obj, cn)
	{
		this.changePage(obj, cn, 'next');
	},

	previous: function(obj, cn)
	{
		this.changePage(obj, cn, 'previous');
	},

	changePage: function(obj, cn, direction)
	{
		var
			container = $(obj).up("div")
			from = parseInt($A(container.getElementsByClassName("qsFromCount"))[0].innerHTML, 10);
			to = parseInt($A(container.getElementsByClassName("qsToCount"))[0].innerHTML, 10);

		this.initNodes();
		this._setFormOptions({
			cn:cn,
			from:from,
			to:to,
			direction:direction
		});
		new LiveCart.AjaxRequest(
			this.nodes.Form,
			this.nodes.Query,
			function(classContainer, transport)
			{
				classContainer.innerHTML = transport.responseText;
				// also trigger onShowResultContainer callback, because common use for this callback is position result and this can change result container size.
				if (this.callbacks && this.callbacks.showResultContainer && typeof this.callbacks.showResultContainer == "function")
				{
					this.callbacks.showResultContainer(this);
				}
			}.bind(this, container.up("div"))
		);
	},

	_setFormOptions: function(options)
	{
		this.nodes.Class.value=options.cn ? options.cn : "";
		this.nodes.From.value=options.from ? options.from : "";
		this.nodes.To.value=options.to ? options.to: "";
		this.nodes.Direction.value=options.direction ? options.direction : "";
	}
}

Backend.QuickSearch.createInstance = function(name, enabledClassNames)
{
	window.quickSearchInstances[name] = new Backend.QuickSearch(name, enabledClassNames);
	return window.quickSearchInstances[name];
}

Backend.QuickSearch.getInstance = function(obj)
{
	var
		i = 0,
		id;
	obj = $(obj);

	// quick search container div has id <instance name>Container
	// remove Container and get instance name.
	while(obj.id.match(/ResultOuterContainer$/) == null)
	{
		if(i>10)
		{
			throw "Can't find QuickSearch instance from passed node";
		}
		obj = obj.up("div");
	}
	id = obj.id.replace(/ResultOuterContainer$/, '');

	if(typeof window.quickSearchInstances[id] == "undefined")
	{
		throw "Can't find QuickSearch instance, probably not initalized";
	}
	return window.quickSearchInstances[id];
}
