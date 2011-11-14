{includeCss file="backend/DatabaseImport.css"}
{includeCss file="backend/CsvImport.css"}

{includeJs file="library/ActiveList.js"}
{includeJs file="library/form/ActiveForm.js"}
{includeJs file="library/form/State.js"}
{includeJs file="library/form/Validator.js"}
{includeJs file="backend/SelectFile.js"}
{includeJs file="backend/Category.js"}
{includeJs file="backend/CsvImport.js"}

{pageTitle}{t _import_csv}{/pageTitle}

{include file="layout/backend/header.tpl"}

<div id="import">

{include file="backend/csvImport/wizardProgress.tpl" class="stepSelect"}

{form action="controller=backend.csvImport action=setFile" method="POST" handle=$form}

	<fieldset>
		<legend>{t _data_type}</legend>
		<label></label>
		{selectfield name="type" options=$types}
	</fieldset>

	<fieldset>
		<legend>{t _select_file}</legend>

		<p class="required">
			{err for="upload"}
				{{label {t _upload_file} }}
				{filefield}
			{/err}
		</p>

		<p>
			<label></label>
			- {t _or} -
		</p>

		<p class="required">
			{err for="atServer"}
				{{label {t _select_at_server} }}
				{textfield id="atServer" class="file"}<input type="button" class="button browse" id="selectAtServer" value="{tn _browse}" />
			{/err}
		</p>
	</fieldset>

	<fieldset>
		<legend>{t _options}</legend>

		<p class="required">
			<label>{t _target_category}</label>
			<label id="targetCategory">
				{foreach from=$catPath item=node name=catPath}
					<a href="#" onclick="Backend.CsvImport.showCategorySelector({$node.ID}); return false;">{$node.name_lang}</a>
					{if !$smarty.foreach.catPath.last}
						&gt;
					{/if}
				{/foreach}
			</label>
			{hidden id="categoryID" name="category"}
		</p>

		<div class="options">
			<p>
				<label>{t _import_action}</label>
				<select name="options[action]">
					<option value="both">{t _add_and_update}</option>
					<option value="add">{t _add_only}</option>
					<option value="update">{t _update_only}</option>
				</select>
			</p>
			<p>
				<label>{t _import_missing_products}</label>
				<select name="options[missing]">
					<option value="keep">{t _keep_intact}</option>
					<option value="disable">{t _disable}</option>
					<option value="delete">{t _delete}</option>
				</select>
			</p>
			<p>
				<label></label>
				{checkbox name="options[transaction]" id="options_transaction" class="checkbox"}
				<label class="checkbox acronym" for="options_transaction"><a>{t _enclose_transaction}<div>{t _transaction_descr}</div></a></label>
			</p>
		</div>

	</fieldset>

	<fieldset class="controls">
		<span class="progressIndicator" style="display: none;"></span>
		<input type="submit" class="submit" value="{tn _continue}" />
		{t _or}
		<a class="cancel" href="{link controller=backend.csvImport}">{t _cancel}</a>
	</fieldset>

{/form}
</div>

{literal}
	<script type="text/javascript">
		Backend.SelectFile.url = {/literal}'{link controller=backend.selectFile}'{literal};
		Backend.Category.links.popup = {/literal}'{link controller=backend.category action=popup}'{literal};
		Event.observe($('selectAtServer'), 'click', function() {new Backend.SelectFile($('atServer')); });
	</script>
{/literal}

{include file="layout/backend/footer.tpl"}