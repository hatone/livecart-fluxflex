{assign var="colspan" value="4"}
{if $productsInSeparateLine}
	{assign var="colspan" value=$colspan-1}
{/if}

{if !$nochanges}
	<div class="orderOverviewControls">
		<a href="{link controller=order}">{t _any_changes}</a>
	</div>
{/if}

<table class="table shipment{if $order.isMultiAddress} multiAddress{/if}" id="payItems">
	<thead>
		<tr>
			<th class="sku">{t _sku}</th>
			{if !$productsInSeparateLine}
				<th class="productName">{t _product}</th>
			{/if}
			<th>{t _price}</th>
			<th>{t _quantity}</th>
			<th>{t _subtotal}</th>
		</tr>
	</thead>
	<tbody>

	{foreach from=$order.shipments key="key" item="shipment"}
		{if $order.isMultiAddress}
			<tr>
				<td colspan="{$colspan}" class="shipmentAddress">
					{$shipment.ShippingAddress.compact}
				</td>
			</tr>
		{/if}
		{include file="order/orderTableDetails.tpl" hideTaxes=true}
	{/foreach}

	{foreach from=$order.discounts item=discount}
		<tr>
			<td colspan="{$colspan}" class="subTotalCaption"><span class="discountLabel">{if $discount.amount > 0}{t _discount}{else}{t _surcharge}{/if}:</span> <span class="discountDesc">{$discount.description}</span></td>
			<td class="amount discountAmount">{$discount.formatted_amount}</td>
		</tr>
	{/foreach}

  	{if !'HIDE_TAXES'|config}
		{if $order.taxes}
			<tr>
				<td colspan="{$colspan}" class="tax">{t _total_before_tax}:</td>
				<td>{$order.formattedTotalBeforeTax.$currency}</td>
			</tr>
		{/if}

		{foreach from=$order.taxes.$currency item="tax"}
			<tr>
				<td colspan="{$colspan}" class="tax">{$tax.name_lang}:</td>
				<td>{$tax.formattedAmount}</td>
			</tr>
		{/foreach}
	{/if}

	<tr>
		<td colspan="{$colspan}" class="subTotalCaption">{t _total}:</td>
		<td class="subTotal">{$order.formattedTotal.$currency}</td>
	</tr>

	</tbody>
</table>

{include file="order/fieldValues.tpl"}