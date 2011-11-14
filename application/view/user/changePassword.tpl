{loadJs form=true}

<div class="userChangePassword">

{include file="user/layout.tpl"}

{include file="user/userMenu.tpl" current="passwordMenu"}
<div id="content">

	<h1>{t _change_pass}</h1>

	<fieldset class="container">
	{form action="controller=user action=doChangePassword" method="POST" handle=$form}

		<p class="required">
			{err for="currentpassword"}
				{{label {t _current_pass}:}}
				{textfield type="password" class="text"}
			{/err}
		</p>

		<p class="required">
			{err for="password"}
				{{label {t _enter_new_pass}:}}
				{textfield type="password" class="text"}
			{/err}
		</p>

		<p class="required">
			{err for="confpassword"}
				{{label {t _reenter_new_pass}:}}
				{textfield type="password" class="text"}
			{/err}
		</p>

		<p>
			<label></label>
			<input type="submit" class="submit" value="{tn _complete_pass_change}" />
			<label class="cancel">
			   {t _or} <a class="cancel" href="{link controller=user}">{t _cancel}</a>
			</label>
		</p>

	{/form}
	</fieldset>

	</div>

</div>

{include file="layout/frontend/footer.tpl"}

</div>