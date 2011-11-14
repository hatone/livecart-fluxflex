<div class="checkoutCompleted">

{pageTitle}{t _order_completed}{/pageTitle}

{include file="layout/frontend/layout.tpl"}

<div id="content">

	<h1>{t _order_completed}</h1>

	{if $order.isPaid}
		{t _completed_paid}
	{else}
		{t _completed_offline}

		{if $transactions.0.serializedData.handlerID}
			{include file="checkout/offlineMethodInfo.tpl" method=$transactions.0.serializedData.handlerID|@substr:-1}
		{/if}
	{/if}

	{include file="checkout/completeOverview.tpl" nochanges=true}
	{include file="checkout/orderDownloads.tpl"}

</div>

{include file="layout/frontend/footer.tpl"}

</div>