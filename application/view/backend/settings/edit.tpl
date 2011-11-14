<h1>{$title}</h1>

{form action="controller=backend.settings action=save" method="post" handle=$form onsubmit="return settings.save(this);" role="settings.update" id="settings" type="multipart/form-data" target="upload"}

{include file="backend/settings/sectionHelp.tpl" key="$sectionKey"}

{foreach from=$layout key=groupName item=fields name="groups"}

	{if !$fields && !$smarty.foreach.groups.first}
		{assign var="subsections" value=false}
		</fieldset>
	{/if}

	<fieldset class="settings">

		{if $groupName}
			<legend>{t $groupName}</legend>
		{/if}

		{foreach from=$fields key="fieldName" item="foo"}
			<div class="setting" id="setting_{$fieldName}" {if 'bool' != $values.$fieldName.type}style="margin-top: 7px; margin-bottom: 7px;"{/if}>
			<p{if 'bool' == $values.$fieldName.type} class="checkbox"{/if}>

				{if 'bool' != $values.$fieldName.type}
					<label for="{$fieldName}" class="setting">{t `$values.$fieldName.title`}:</label>
				{/if}

			<fieldset class="error">
				{if 'string' == $values.$fieldName.type}
					{textfield class="text wide" name="$fieldName" id="$fieldName"}
				{elseif 'image' == $values.$fieldName.type}
					{filefield name="$fieldName" id="$fieldName"}
					<image class="settingImage" src="{$fieldName|config}" />
				{elseif 'longtext' == $values.$fieldName.type}
					{textarea class="tinyMCE" name="$fieldName" id="$fieldName"}
				{elseif 'num' == $values.$fieldName.type || 'float' == $values.$fieldName.type}
					{textfield class="text number" name="$fieldName" id="$fieldName"}
				{elseif 'bool' == $values.$fieldName.type}
					{checkbox class="checkbox" name="$fieldName" id="$fieldName" value="1"}
					<label class="checkbox" for="{$fieldName}">{t `$values.$fieldName.title`}</label>
				{elseif is_array($values.$fieldName.type)}
					{if 'multi' == $values.$fieldName.extra}
						<div class="multi">
						{foreach from=$values.$fieldName.type item="value" key="key"}
							<p>
							{checkbox name="`$fieldName`[`$key`]" class="checkbox" value=1}
							<label for="{$fieldName}[{$key}]" class="checkbox">{$value}</label>
							</p>
						{/foreach}
							<div class="clear"></div>
						</div>
					{else}
						{selectfield options=$values.$fieldName.type name="$fieldName" id="$fieldName"}
					{/if}
				{/if}
				<div class="errorText hidden"></div>
			</fieldset>
			</p>
			</div>
		{foreachelse}
			{assign var="subsections" value=true}
		{/foreach}

	{if $fields || $smarty.foreach.groups.last}
		</fieldset>
	{/if}

{/foreach}

{if $subsections}
	</fieldset>
{/if}

{language}
	{foreach from=$multiLingualValues key="fieldName" item="foo"}
	<p>
		<label for="{$fieldName}_{$lang.ID}" class="setting">{t `$values.$fieldName.title`}:</label>

		<fieldset class="error">
			{if $types.$fieldName == 'longtext'}
				{textarea class="tinyMCE" name="`$fieldName`_`$lang.ID`" id="`$fieldName`_`$lang.ID`"}
			{else}
				{textfield class="text wide" name="`$fieldName`_`$lang.ID`" id="`$fieldName`_`$lang.ID`"}
			{/if}
			<div class="errorText hidden"></div>
		</fieldset>
	</p>
	{/foreach}
{/language}

<input type="hidden" name="id" value="{$id}" />

<fieldset class="controls">
	<span class="progressIndicator" style="display: none;"></span>
	<input type="submit" value="{tn _save}" class="submit" />
	{t _or}
	<a class="cancel" href="#" onclick="return false;">{t _cancel}</a>
</fieldset>
{/form}

{literal}
<script type="text/javascript">
	new Backend.Settings.Editor($('settings'), window.settings);
</script>
{/literal}