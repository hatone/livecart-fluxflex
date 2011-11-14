{* assign range type as name *}
{php}
	$service = $this->get_template_vars('service');
	if($service['rangeType'] == ShippingService::WEIGHT_BASED)
	{
		$this->assign('rangeTypeName', 'weight');
	}
	else if($service['rangeType'] == ShippingService::SUBTOTAL_BASED)
	{
		$this->assign('rangeTypeName', 'subtotal');
	}
{/php}

<table style="width:100%;">
	<tbody>
		<tr>
			<td valign="top" width="150">

<table id="ratesTableLabels_{$service.DeliveryZone.ID}_{$service.ID}">
	<tbody>
		<tr class="weight weightRow">
			<td class="fixedHeader">{tip _weight}
				(<span class="UnitConventer_UnitsName"></span>)
				<a class="UnitConventer_SwitchToUnits" href="javascript:void(0);"></a>
				<input class="UnitConventer_UnitsType" value="METRIC" type="hidden">
			</td>
		</tr>
		<tr class="subtotal subtotalRow">
			<td class="fixedHeader">{tip _subtotal}
			</td>
		</tr>
		<tr>
			<td class="fixedHeader">{tip _flat_charge} ({$defaultCurrencyCode})</td>
		</tr>
		<tr>
			<td class="fixedHeader">{tip _per_item_charge} ({$defaultCurrencyCode})</td>
		</tr>
		<tr class="weight">
			<td class="fixedHeader">{tip _per_kg_charge} ({$defaultCurrencyCode})</td>
		</tr>
		<tr class="subtotal">
			<td class="fixedHeader">{tip _subtotal_percent_charge} (%)</td>
		</tr>
	</tbody>
</table>

			</td>
			<td valign="top" style="text-align:left;">

<div class="ratesTableContainerScroll" style="float:left;">
	<table id="ratesTableContainer_{$service.DeliveryZone.ID}_{$service.ID}">
		<tbody>
			<tr class="weight weightRow">
				{foreach from=$shippingRates item=rate name=weightRowLoop key=key}
					<td>
						<div class="rangeStart">
							{if $smarty.foreach.weightRowLoop.first}
								{include
									file="backend/shippingService/rangeStartField.tpl"
									value=$rate.weightRangeStart
									name="rate_`$rate.ID`_weightRangeStart"
								}
							{else}
								<span class="rangeStartValue">
								{if $key > 0}
									{math equation="a-1" a=$key assign="prevKey"}
									{$shippingRates[$prevKey].weightRangeEnd}
								{/if}
								</span>
							{/if}
							-
						</div>

						{if $rangeTypeName == 'weight'}
							{textfield
								value=$rate.weightRangeEnd
								class="number UnitConventer_NormalizedWeight"
								name="rate_`$rate.ID`_weightRangeEnd"
							}
							
							{textfield class="number UnitConventer_HiValue"}<span class="UnitConventer_HiValueAbbr"></span>
							{textfield class="number UnitConventer_LoValue"}<span class="UnitConventer_LoValueAbbr"></span>
							{textfield class="number UnitConventer_MergedValue"}
						{else}
							{textfield class="number UnitConventer_NormalizedWeight" name="rate_`$rate.ID`_weightRangeEnd"}
						{/if}
					</td>
				{/foreach}
				<td>
					<div class="rangeStart">
						{if count($shippingRates) == 0}
							{* there is no first range start field, threfore add here! *}
							{include
								file="backend/shippingService/rangeStartField.tpl"
								value="1"
								name="rate_new0_weightRangeStart"
							}
						{else}
							<span class="rangeStartValue">{$shippingRates[$key].weightRangeEnd}</span>
						{/if} -
					</div>
					{textfield
						class="number UnitConventer_NormalizedWeight"
						name="rate_new0_weightRangeEnd"
					}
					{textfield class="number UnitConventer_HiValue"}<span class="UnitConventer_HiValueAbbr"></span>
					{textfield class="number UnitConventer_LoValue"}<span class="UnitConventer_LoValueAbbr"></span>
					{textfield class="number UnitConventer_MergedValue"}
				</td>
			</tr>

			
			<tr class="subtotal subtotalRow">
				{foreach from=$shippingRates item=rate name=subtotalRowLoop key=key}
					<td>
						<div class="rangeStart">
							{if $smarty.foreach.subtotalRowLoop.first}
								{textfield class="number rangeStartValue"
									value=$rate.subtotalRangeStart
									name="rate_`$rate.ID`_subtotalRangeStart"}
							{else}
								<span class="rangeStartValue">
								{if $key > 0}
									{math equation="a-1" a=$key assign="prevKey"}
									{$shippingRates[$prevKey].subtotalRangeEnd}
								{/if}
								</span>
							{/if}
							-
						</div>
						
						{if $rangeTypeName == 'subtotal'}
							{textfield value=$rate.subtotalRangeEnd class="number" name="rate_`$rate.ID`_subtotalRangeEnd"}
						{else}
							{textfield class="number" name="rate_`$rate.ID`_subtotalRangeEnd"}
						{/if}
					</td>
				{/foreach}
				<td>
					<div class="rangeStart">
						{if count($shippingRates) == 0}
							{* there is no first range start field, threfore add here! *}
							{textfield class="number rangeStartValue"
								value="0"
								name="rate_new0_subtotalRangeStart"}
						{else}
							<span class="rangeStartValue">{$shippingRates[$key].subtotalRangeEnd}</span>
						{/if}
						-
					</div>
				{textfield class="number" name="rate_new0_subtotalRangeEnd"}</td>
			</tr>

			<tr>
				{foreach from=$shippingRates item=rate}
					<td>{textfield value=$rate.flatCharge class="number" name="rate_`$rate.ID`_flatCharge"}</td>
				{/foreach}
				<td>{textfield class="number" name="rate_new0_flatCharge"}</td>
			</tr>

			<tr>
				{foreach from=$shippingRates item=rate}
					<td>{textfield value=$rate.perItemCharge class="number" name="rate_`$rate.ID`_perItemCharge"}</td>
				{/foreach}
				<td>{textfield class="number" name="rate_new0_perItemCharge"}</td>
			</tr>

			<tr class="weight">
				{foreach from=$shippingRates item=rate}
					<td>
						{if $rangeTypeName == 'weight'}
							{textfield value=$rate.perKgCharge class="number" name="rate_`$rate.ID`_perKgCharge"}
						{else}
							{textfield class="number" name="rate_`$rate.ID`_perKgCharge"}
						{/if}
					</td>
				{/foreach}
				<td>{textfield class="number" name="rate_new0_perKgCharge"}</td>
			</tr>

			<tr class="subtotal">
				{foreach from=$shippingRates item=rate}
					<td>
						{if $rangeTypeName == 'subtotal'}
							{textfield value=$rate.subtotalPercentCharge class="number" name="rate_`$rate.ID`_subtotalPercentCharge"}
						{else}
							{textfield class="number" name="rate_`$rate.ID`_subtotalPercentCharge"}
						{/if}
					</td>
				{/foreach}
				<td>{textfield class="number" name="rate_new0_subtotalPercentCharge"}</td>
			</tr>
		</tbody>
	</table>
</div>

			</td>
		</tr>
	</tbody>
</table>

<script type="text/javacript">
	new Backend.DeliveryZone.WeightTable(
		$("ratesTableContainer_{$service.DeliveryZone.ID}_{$service.ID}"),
		"{$rangeTypeName}",
		{if !$service.ID}true{else}false{/if}
	);
</script>