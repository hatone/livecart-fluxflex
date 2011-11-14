<span>
	<fieldset class="container">
		<div class="productRelationship_image">
			{if $product.DefaultImage}
				{img src=$product.DefaultImage.paths[1] alt=$product.DefaultImage.title title=$product.DefaultImage[1].title }
			{/if}
		</div>
		{if $template}
			{include file=$template}
		{/if}
		<span class="productRelationship_title">{$product.name_lang}</span>
		<a href="{backendProductUrl product=$product}" onclick="Backend.Product.openProduct({$product.ID}); return false;" class="openRelatedProduct"></a>
	</fieldset>
	<div class="clear: both"></div>
</span>