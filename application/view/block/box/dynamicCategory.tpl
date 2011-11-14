{literal}
<script type="text/javascript"><!--//--><![CDATA[//><!--

sfHover = function() {
	var sfEls = document.getElementById("dynamicNav").getElementsByTagName("LI");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);

//--><!]]></script>
{/literal}

{defun name="dynamicCategoryTree" node=false filters=false}
	{if $node}
		<ul id="dynamicNav">
		{foreach from=$node item=category}
				<li class="{if $category.parentNodeID == 1}topCategory{/if} {if $category.lft <= $currentCategory.lft && $category.rgt >= $currentCategory.rgt} dynCurrent{/if}{if $category.subCategories} hasSubs{else} noSubs{/if}">
					<a href="{categoryUrl data=$category filters=$category.filters}">{$category.name_lang}</a>
					{if 'DISPLAY_NUM_CAT'|config}
						<span class="count">(&rlm;{$category.count})</span>
					{/if}
					{if $category.subCategories}
		   				{fun name="dynamicCategoryTree" node=$category.subCategories}
					{/if}
				</li>
		{/foreach}
		</ul>
	{/if}
{/defun}

<div class="box categories dynamicMenu">
	<div class="title">
		<div>{t _categories}</div>
	</div>

	<div class="content">
		<fieldset class="container">
		{fun name="dynamicCategoryTree" node=$categories}
		</fieldset>
	</div>

	<div class="clear"></div>
</div>