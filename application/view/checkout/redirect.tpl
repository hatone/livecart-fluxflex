<body onload="document.getElementsByTagName('form')[0].submit();">
	<p>{t _redirecting_to_payment_page}</p>
	<form action="{$url}" method="post">
		{foreach from=$params item=param key=key}
			<input type="hidden" name="{$key}" value="{$param|escape}" />
		{/foreach}
	</form>
</body>