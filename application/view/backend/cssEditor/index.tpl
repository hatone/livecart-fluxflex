{includeJs file="library/livecart.js"}
{includeJs file="library/form/ActiveForm.js"}
{includeJs file="library/form/State.js"}
{includeJs file="library/form/Validator.js"}
{includeJs file="library/dhtmlxtree/dhtmlXCommon.js"}
{includeJs file="library/dhtmlxtree/dhtmlXTree.js"}
{includeJs file="backend/CssEditor.js"}
{includeJs file="backend/Theme.js"}
{includeJs file="library/TabControl.js"}
{includeCss file="library/TabControl.css"}
{includeJs file="library/editarea/edit_area_full.js"}
{includeCss file="backend/CssEditor.css"}
{includeCss file="library/dhtmlxtree/dhtmlXTree.css"}

{pageTitle help="customize.css"}{t _edit_css}{/pageTitle}
{include file="layout/backend/header.tpl"}

<div id="templatePageContainer">
	<div class="treeContainer">
		<div id="templateBrowser" class="treeBrowser"></div>
	</div>
	<div class="treeManagerContainer">
		<div id="templateTabContainer" class="tabContainer" style="height:100%">
			<div class="templateContent">
				<div id="loadingNewsletter" style="display: none; position: absolute; text-align: center; width: 100%; padding-top: 200px; z-index: 50000;">
					<span style="padding: 40px; background-color: white; border: 1px solid black;">{t _loading_newsletter}<span class="progressIndicator"></span></span>
				</div>
				<ul class="tabList tabs"></ul>
				<div class="sectionContainer" style="display:none;"></div>
				<div class="notabsContainer">{include file="backend/cssEditor/emptyPage.tpl"}</div>
			</div>
		</div>
	</div>
</div>

{literal}
<script type="text/javascript">
	var settings = new Backend.CssEditor({/literal}{$categories}{literal});
	settings.urls['edit'] = '{/literal}{link controller=backend.cssEditor action=edit query="file=_id_&tabid=_tabid_"}{literal}';
	settings.urls['empty'] = '{/literal}{link controller=backend.cssEditor action=emptyPage}{literal}';
	settings.tabControl =  TabControl.prototype.getInstance(
		'templateTabContainer',
		Backend.CssEditor.prototype.getTabUrl,
		Backend.CssEditor.prototype.getContentTabId,
		{
			afterClick:settings.tabAfterClickCallback.bind(settings)
		}
	);
</script>
{/literal}

{include file="layout/backend/footer.tpl"}
