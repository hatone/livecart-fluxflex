{form action="controller=user action=doRegister" method="POST" handle=$regForm}

	{* field required=true name="firstName" label=_your_first_name type=textfield *}

	<p class="required">
		{err for="firstName"}
			{{label {t _your_first_name}:}}
			{textfield class="text"}
		{/err}
	</p>

	<p class="required">
		{err for="lastName"}
			{{label {t _your_last_name}:}}
			{textfield class="text"}
		{/err}
	</p>

	<p>
		{err for="companyName"}
			{{label {t _company_name}:}}
			{textfield class="text"}
		{/err}
	</p>

	<p class="required">
		{err for="email"}
			{{label {t _your_email}:}}
			{textfield class="text"}
		{/err}
	</p>

	{include file="user/block/passwordFields.tpl" required=true}

	{include file="block/eav/fields.tpl" item=$user filter="isDisplayed"}

	{block FORM-SUBMIT-REGISTER}

	<p class="submit">
		<label>&nbsp;</label>
		<input type="submit" class="submit" value="{tn _complete_reg}" />
		{if $request.return}
			<input type="hidden" name="return" value="{$request.return|escape}" />
		{/if}
	</p>

{/form}