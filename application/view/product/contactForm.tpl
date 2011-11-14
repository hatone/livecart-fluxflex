<div id="contactFormSection" class="productSection contactFormSection">
<h2>{t _inquire}<small>{t _inquire_title}</small></h2>

<div>
{form action="controller=product action=sendContactForm" method="POST" handle=$contactForm id="productContactForm" onsubmit="new Product.ContactForm(this); return false;"}
	<p class="required">
		{err for="name"}
			{{label {t _inquiry_name}:}}
			{textfield class="text"}
		{/err}
	</p>

	{* anti-spam *}
	<div style="display: none;">
		{err for="surname"}
			{{label Your surname:}}
			{textfield class="text"}
		{/err}
	</div>

	<p class="required">
		{err for="email"}
			{{label {t _inquiry_email}:}}
			{textfield class="text"}
		{/err}
	</p>

	<p class="required">
		{err for="msg"}
			{{label {t _inquiry_msg}:}}
			{textarea}
		{/err}
	</p>

	<p>
		<label>&nbsp;</label>
		<span class="progressIndicator" style="display: none;"></span>
		<input type="submit" class="submit" value="{t _form_submit}" />
	</p>

	<input type="hidden" name="id" value="{$product.ID}" />

{/form}
<div class="clear"></div>
</div>
</div>