<tr>
	<td colspan="{math equation="$extraColspanSize + 3"}" class="subTotalCaption">{t _total}:</td>
	<td class="subTotal">{$cart.formattedTotal.$currency}</td>
	{$GLOBALS.cartUpdate|@array_shift}
</tr>
