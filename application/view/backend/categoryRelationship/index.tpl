{defun name="catPath" category=null}
	{if $category}
		{if $category.ParentNode}
			{fun name="catPath" category=$category.ParentNode} &gt;
		{/if}
		{$category.name_lang}
	{/if}
{/defun}

<ul class="menu">
	<li class="addAdditionalCategory"><a href="#">{t _add_category}</a></li>
</ul>
<ul class="additionalCategories activeList_add_sort">
</ul>
<li class="categoryTemplate" style="display: none;">
	<span class="recordDeleteMenu">
		<img src="image/silk/cancel.png" class="recordDelete" />
		<span class="progressIndicator" style="display: none;"></span>
	</span>
	<span class="categoryName"></span>
</li>

<script type="text/javascript">
	new Backend.CategoryRelationship($('tabRelatedCategoryContent_{$category.ID}'), {json array=$category}, {json array=$categories});
</script>