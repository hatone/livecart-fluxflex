<h1>{$fileName}</h1>

<div 
	onclick="{literal}TabControl.prototype.getInstance('tabContainer').activateTab($('tabColors'));{/literal}"
	class="warning cssAndStyleTab" id="notice_changes_colors_and_styles_tab_{$tabid}" style="display:none;"
>{t _notice_changes_colors_and_styles_tab}</div>

{form handle=$form action="controller=backend.cssEditor action=save" method="POST" class="templateForm" id="templateForm_`$tabid`"}
	{if $new || $template.isCustomFile}
		<p>
			{{err for="fileName"}}
				<label style="margin-top: 9px;">{t _template_file_name}:</label>
				{textfield class="text"}
			{/err}
		</p>
		<div class="clear" style="margin-bottom: 1em;"></div>
	{/if}

	{textarea name="code" id="code_{$tabid}" class="code"}
	{hidden name="file" id="file_{$tabid}"}

	{if $new}
		{hidden name="new" value="true"}
	{/if}

	<fieldset class="controls" {denied role="template.save"}style="display: none;"{/denied}>
		<span class="progressIndicator" style="display: none;"></span>
		<input type="submit" class="submit" value="{tn _save_css}" />
		{if isset($noTabHandling) == false}
			{t _or}
			<a id="cancel_{$tabid}" class="cancel" href="{link controller="backend.cssEditor"}">{t _cancel}</a>
		{/if}
	</fieldset>
{/form}

{literal}
	<script type="text/javascript">
		Backend.isCssEdited["{/literal}{$tabid}{literal}"] = false;
		if (Backend.Theme.prototype.isStyleTabChanged("{/literal}{$tabid}{literal}"))
		{
			Backend.Theme.prototype.styleTabChanged("{/literal}{$tabid}{literal}");
		}
		$('code_{/literal}{$tabid}{literal}').value = {/literal}decode64("{$code}");{literal};
		editAreaLoader.baseURL = "{/literal}{baseUrl}javascript/library/editarea/{literal}";
	</script>
{/literal}

{if $noTabHandling}
	<script type="text/javascript">
		new Backend.CssEditorHandler($('templateForm_{$tabid}'), null, '{$tabid}');
	</script>
{/if}