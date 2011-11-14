{if $pages}
<div class="informationMenu">
	{foreach from=$pages item=page name="pages"}
		<span class="infoLink" id="static_{$page.ID}"><a href="{pageUrl data=$page}">{$page.title_lang}</a></span>
		{if !$smarty.foreach.pages.last}
		<span class="sep"> | </span>
		{/if}
	{/foreach}

	<div class="extraPages">
		<span class="infoLink" id="contactFormLink"><a href="{link controller=contactForm}">{t _contact}</a></span>
		<span class="sep"> | </span> <span class="infoLink" id="allManufacturersLink"><a href="{link controller=manufacturers}">{t _manufacturers}</a></span>
		<span class="sep"> | </span> <span class="infoLink" id="allCategoriesLink"><a href="{link controller=category action=all}">{t _categories}</a></span>
		<span class="sep"> | </span> <span class="infoLink" id="allProductsLink"><a href="{link controller=category action=allProducts}">{t _all_products}</a></span>
	</div>
</div>
{/if}