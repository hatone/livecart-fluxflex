<div id="imageContainer">
	<div id="largeImage" class="{if $images|@count == 0}missingImage{/if} {if $images|@count > 1}multipleImages{/if}">
		{if $product.DefaultImage.paths.3}
			<a onclick="Product.Lightbox2Gallery.start(this); return false;" href="{$product.DefaultImage.paths.4}" title="{$product.DefaultImage.title_lang|escape}" target="_blank">
				{img src=$product.DefaultImage.paths.3 alt=$product.DefaultImage.title_lang|escape id="mainImage"}
			</a>
		{else}
			{img src='MISSING_IMG_LARGE'|config alt="" id="mainImage"}
		{/if}
	</div>
	{if $images|@count > 1}
		<div id="moreImages">
			{foreach from=$images item="image"}
				<a href="{$image.paths.4}" target="_blank" onclick="return false;">{img src=$image.paths.1 id="img_`$image.ID`" alt=$image.name_lang|escape onclick="return false;"}</a>
			{/foreach}
		</div>
	{/if}
	{if $images|@count > 0}
		{* lightbox2 gallery images *}
		<div class="hidden">
			{foreach from=$images item="image"}
				<a rel="lightbox[product]" href="{$image.paths.4}" target="_blank" onclick="return false;">{img src=$image.paths.1 id="img_`$image.ID`" alt=$image.name_lang|escape onclick="return false;"}</a>
			{/foreach}
		</div>
	{/if}
</div>

{literal}
<script type="text/javascript">
{/literal}
	var imageData = $H();
	var imageDescr = $H();
	var imageProducts = $H();
	{foreach from=$images item="image"}
		imageData[{$image.ID}] = {json array=$image.paths};
		imageDescr[{$image.ID}] = {json array=$image.title_lang};
		imageProducts[{$image.ID}] = {json array=$image.productID};
	{/foreach}
	new Product.ImageHandler(imageData, imageDescr, imageProducts, {if $enlargeProductThumbnailOnMouseOver}true{else}false{/if});

	var loadingImage = 'image/loading.gif';
	var closeButton = 'image/silk/gif/cross.gif';
</script>