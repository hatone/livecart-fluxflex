
	<div class="clear"></div>
	</div>

	</div>
	<div id="pageFooter">
		<div style="float: left;">
			{'POWERED_BY_BACKEND'|config}
		</div>
		{if 'BACKEND_SHOW_FOOTER_LINKS'|config}
			<div id="supportLinks" style="float: right; padding-left: 50px;">
				<a href="http://support.livecart.com" target="_blank">Customer Support</a>
				/
				<a href="http://forums.livecart.com" target="_blank">Forums</a>
			</div>
		{/if}
		<div id="footerStretch">
			&nbsp;
		</div>
	</div>
</div>


<script type="text/javascript">
	Backend.internalErrorMessage = '{t _internal_error_have_accurred}';
	new Backend.LayoutManager();
</script>

{if !'DISABLE_TOOLBAR'|config}
	{block FOOTER_TOOLBAR}
{/if}

</body>
</html>


