<span class="activeGridMass" {denied role="product.mass"}style="visibility: hidden;"{/denied} id="manufacturerMass" >

	{form action="controller=backend.manufacturer action=processMass" method="POST" handle=$massForm onsubmit="return false;"}

	<input type="hidden" name="filters" value="" />
	<input type="hidden" name="selectedIDs" value="" />
	<input type="hidden" name="isInverse" value="" />

	{t _with_selected}:
	<select name="act" class="select">
		<option value="delete">{t _delete}</option>
	</select>

	<span class="bulkValues" style="display: none;"></span>

	<input type="submit" value="{tn _process}" class="submit" />
	<span class="progressIndicator" style="display: none;"></span>

	{/form}

</span>