{defun name="catPath" category=null}
	{if $category}
		{if $category.ParentNode}
			{fun name="catPath" category=$category.ParentNode} &gt;
		{/if}
		{$category.name_lang}
	{/if}
{/defun}

<fieldset>
	<legend>{t _main_category}</legend>
	<ul class="menu">
		<li class="changeMainCategory"><a href="#">{t _change_category}</a></li>
	</ul>
	<div class="mainCategory">
		<span class="progressIndicator" style="display: none;"></span>
		<span class="categoryName">
			{fun name="catPath" category=$product.Category}
		</span>
	</div>
</fieldset>

<fieldset>
	<legend><span class="progressIndicator" style="display: none;"></span>{t _additional_categories}</legend>
	<ul class="menu">
		<li class="addAdditionalCategory"><a href="#">{t _add_category}</a></li>
	</ul>
	<ul class="additionalCategories">
	</ul>
	<li class="categoryTemplate" style="display: none;">
		<span class="recordDeleteMenu">
			<img src="image/silk/cancel.png" class="recordDelete" />
			<span class="progressIndicator" style="display: none;"></span>
		</span>
		<span class="categoryName"></span>
	</li>
</fieldset>

<script type="text/javascript">
	new Backend.ProductCategory($('tabProductCategories_{$product.ID}Content'), {json array=$product}, {json array=$categories});
</script>