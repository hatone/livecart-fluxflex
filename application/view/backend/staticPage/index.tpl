{includeJs file="library/dhtmlxtree/dhtmlXCommon.js"}
{includeJs file="library/dhtmlxtree/dhtmlXTree.js"}
{includeJs file="library/form/Validator.js"}
{includeJs file="library/form/ActiveForm.js"}
{includeJs file="backend/StaticPage.js"}

{includeCss file="library/dhtmlxtree/dhtmlXTree.css"}
{includeCss file="backend/StaticPage.css"}

{pageTitle help="content.pages"}{t _static_pages}{/pageTitle}

{include file="backend/eav/includes.tpl"}
{include file="layout/backend/header.tpl"}

<div id="staticPageContainer">
	<div class="treeContainer">
		<div id="pageBrowser" class="treeBrowser"></div>

		<ul class="verticalMenu">
			<li id="addMenu" class="addTreeNode" {denied role="page.create"}style="display: none;"{/denied}><a href="" onclick="pageHandler.showAddForm(); return false;">{t _add_new}</a></li>
			<li id="moveUpMenu" class="moveUpTreeNode" {denied role="page.sort"}style="display: none;"{/denied}><a href="" onclick="pageHandler.moveUp(); return false;">{t _move_up}</a></li>
			<li id="moveDownMenu" class="moveDownTreeNode" {denied role="page.sort"}style="display: none;"{/denied}><a href="" onclick="pageHandler.moveDown(); return false;">{t _move_down}</a></li>
			<li id="removeMenu" class="removeTreeNode" {denied role="page.remove"}style="display: none;"{/denied}><a href="" onclick="pageHandler.deleteSelected(); return false;">{t _remove}</a></li>
		</ul>
		
		<div id="confirmations">
			<div id="yellowZone">
				<div id="staticPageAdded" class="yellowMessage" style="display: none;">
					{img class="closeMessage" src="image/silk/cancel.png"}
					<div>{t _new_page_was_successfully_added}</div>
				</div>
			</div>
		</div>
	</div>

	<div class="treeManagerContainer maxHeight h--100">
		<span id="settingsIndicator" class="progressIndicator" style="display: none;"></span>
				
		<div id="pageContent" class="maxHeight">
			{include file="backend/staticPage/emptyPage.tpl"}
		</div>
	</div>
</div>

{literal}
<script type="text/javascript">
	var pageHandler = new Backend.StaticPage({/literal}{$pages}{literal});

	pageHandler.urls['edit'] = '{/literal}{link controller=backend.staticPage action=edit}?id=_id_{literal}';
	pageHandler.urls['add'] = '{/literal}{link controller=backend.staticPage action=add}{literal}';	
	pageHandler.urls['delete'] = '{/literal}{link controller=backend.staticPage action=delete}?id=_id_{literal}';
	pageHandler.urls['moveup'] = '{/literal}{link controller=backend.staticPage action=reorder}?order=up&id=_id_{literal}';
	pageHandler.urls['movedown'] = '{/literal}{link controller=backend.staticPage action=reorder}?order=down&id=_id_{literal}';
	pageHandler.urls['empty'] = '{/literal}{link controller=backend.staticPage action=emptyPage}{literal}';	
	pageHandler.urls['create'] = '{/literal}{link controller=backend.staticPage action=create}{literal}';	
	pageHandler.urls['update'] = '{/literal}{link controller=backend.staticPage action=update}{literal}';	

	// methods that need urls
	pageHandler.afterInit();

//	Event.observe(window, 'load', function() {pageHandler.activateCategory('00-store');})
</script>
{/literal}

<div class="clear"></div>

<div id="pageDelConf" style="display: none;">{t _del_conf}</div>

{include file="layout/backend/footer.tpl"}