{includeJs file="library/livecart.js"}
{includeJs file="library/dhtmlxtree/dhtmlXCommon.js"}
{includeJs file="library/dhtmlxtree/dhtmlXTree.js"}
{includeJs file="library/rico/ricobase.js"}
{includeJs file="library/rico/ricoLiveGrid.js"}
{includeJs file="library/ActiveGrid.js"}
{includeJs file="backend/SelectFile.js"}

{includeCss file="library/dhtmlxtree/dhtmlXTree.css"}
{includeCss file="library/ActiveGrid.css"}
{includeCss file="backend/Category.css"}
{includeCss file="backend/SelectFile.css"}

{pageTitle}{t _select_file}{/pageTitle}

{include file="layout/backend/meta.tpl"}

{literal}
<style>
	body
	{
		background-image: none;
	}
</style>
{/literal}

<div id="pageTitleContainer">
	<div id="pageTitle">{$PAGE_TITLE}</div>
	<div id="breadcrumb_template" class="dom_template">
		<span class="breadcrumb_item"><a href=""></a></span>
		<span class="breadcrumb_separator"> &gt; </span>
		<span class="breadcrumb_lastItem"></span>
	</div>
	<div id="breadcrumb"></div>
</div>

<div id="popupCategoryContainer" class="treeContainer">

	<div id="categoryBrowser" class="treeBrowser"> </div>

</div>

<div id="fileContainer">

{activeGrid
	prefix="files"
	id=0
	controller="backend.selectFile" action="lists"
	displayedColumns=$displayedColumns
	availableColumns=$availableColumns
	totalCount=0
	filters=$filters
	container="fileContainer"
	count="backend/selectFile/count.tpl"
}

</div>

{literal}
<script type="text/javascript">
	var inst = window.selectFileInstance;
	inst.grid = window.activeGrids['files_0'];

	inst.grid.ricoGrid.metaData.options.largeBufferSize = 100;

	inst.links = {};
	inst.links.categoryRecursiveAutoloading = '{/literal}{link controller=backend.selectFile action=xmlRecursivePath}{literal}';
	inst.links.categoryAutoloading = '{/literal}{link controller=backend.selectFile action=xmlBranch}{literal}';

	inst.init();
	inst.addCategories({/literal}{json array=$root}{literal});
	inst.treeBrowser.setXMLAutoLoading(inst.links.categoryAutoloading);
	inst.addCategories({/literal}{json array=$directoryList}{literal});

	inst.initPage();

	inst.loadDirectory({/literal}{json array=$current}{literal});
</script>
{/literal}

</body>
</html>