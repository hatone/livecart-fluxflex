<h1>
	{$displayFileName}
</h1>

{form handle=$form action="controller=backend.template action=saveEmail" method="POST" class="templateForm" id="templateForm_`$tabid`"}

	{if !$template.isFragment}
	<p>
		<label class="wide">{t _subject}:</label>
		{textfield name="subject" id="subject_`$tabid`" class="text wide"}
	</p>
	{/if}

	<p>
		{if !$template.isFragment}
			<label class="wide">{t _body}:</label>
		{/if}
		{textarea name="body" id="body_`$tabid`" class="body"}
	</p>

	{if $template.hasPlainText}
	<p>
		<label class="wide">{t _html_version}:</label>
		{textarea name="html" id="html_`$tabid`" class="body"}
	</p>
	{/if}

	{if $fileName|@substr:0:11 != 'email/block'}
		{language}
			{if !$template.isFragment}
				<p>
					<label class="wide">{t _subject}:</label>
					{textfield name="subject_`$lang.ID`" class="text wide"}
				</p>
			{/if}

			<p>
				{if !$template.isFragment}
					<label class="wide">{t _body}:</label>
				{/if}
				{textarea name="body_`$lang.ID`" id="body_`$tabid`_`$lang.ID`" class="body"}
			</p>

			{if $template.hasPlainText}
			<p>
				<label class="wide">{t _html_version}:</label>
				{textarea name="html_`$lang.ID`" id="html_`$tabid`_`$lang.ID`" class="body"}
			</p>
			{/if}
		{/language}
	{/if}

	{hidden name="file" id="file_`$tabid`"}

	<fieldset class="controls" {denied role="template.save"}style="display: none;"{/denied}>
		<span class="progressIndicator" style="display: none;"></span>
		<input type="submit" class="submit" value="{tn _save_template}" />
		{t _or}
		<a id="cancel_{$tabid}" class="cancel" href="{link controller="backend.template"}">{t _cancel}</a>
	</fieldset>
{/form}

{literal}
<script type="text/javascript">
	$('body_{/literal}{$tabid}{literal}').value = {/literal}decode64("{$template.bodyEncoded}");{literal};
	editAreaLoader.baseURL = "{/literal}{baseUrl}javascript/library/editarea/{literal}";
</script>
{/literal}