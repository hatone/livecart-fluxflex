<div class="image">
	<span style="font-size: 1px;">&nbsp;</span>
	<a href="{productUrl product=$product filterChainHandle=$filterChainHandle}">
	{if $product.DefaultImage.ID}
		{img src=$product.DefaultImage.paths.2 alt=$product.name_lang|escape}
	{else}
		{img src='MISSING_IMG_SMALL'|config alt=$product.name_lang|escape}
	{/if}
	</a>
</div>