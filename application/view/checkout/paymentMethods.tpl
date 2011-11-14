{include file="checkout/block/ccForm.tpl"}

{if $otherMethods}
	{if 'CC_ENABLE'|config}
		<h2>{t _other_methods}</h2>
	{else}
		<h2>{t _select_payment_method}</h2>
	{/if}

	<div id="otherMethods">
		{foreach from=$otherMethods item=method}
			{if $id}{assign var="query" value="order=`$id`"}{/if}
			<a href="{link controller=checkout action=redirect id=$method query=$query}"><img src="image/payment/{$method}.gif" /></a>
		{/foreach}
	</div>
{/if}
