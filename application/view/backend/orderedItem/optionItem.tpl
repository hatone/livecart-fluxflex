<div{if $option.isRequired} class="required"{/if} class="productOption">
	{if $option.fieldName}{assign var=fieldName value=$option.fieldName}{else}{assign var=fieldName value="option_`$option.ID`"}{/if}
	{if 0 == $option.type}
		{{err for="`$fieldName`"}}
			<label></label>
			{checkbox class="checkbox"}
			<label for={$fieldName} class="checkbox">
				{$option.name_lang}
				{if $option.DefaultChoice.priceDiff != 0}
					({$option.DefaultChoice.formattedPrice.$currency})
				{/if}
			</label>
		{/err}
	{else}
		<label class="field">{$option.name_lang}</label>
			{{err for="`$fieldName`"}}
			{if 1 == $option.type}
				<fieldset class="error">
				<select name="{$fieldName}">
					{foreach from=$option.choices item=choice}
						<option value="{$choice.ID}"{if $selectedChoice.Choice.ID == $choice.ID} selected="selected"{/if}>
							{$choice.name_lang}
							{if $choice.priceDiff != 0}
								({$choice.formattedPrice.$currency})</label>
							{/if}
						</option>
					{/foreach}
				</select>
			{elseif 2 == $option.type}
				{textfield class="text"}
			{elseif 3 == $option.type}
				{filefield name="upload_`$fieldName`"}
				{hidden name=$fieldName}
				{error for="upload_`$fieldName`"}<div class="errorText">{$msg}</div>{/error}
			{/if}
		{/err}
	{/if}
</div>
<div class="clear"></div>