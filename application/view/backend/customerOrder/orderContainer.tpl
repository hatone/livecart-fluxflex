<fieldset id="orderManagerContainer" class="treeManagerContainer maxHeight h--100" style="display: none;">

	<fieldset class="container">
		<ul class="menu">
			<li class="done">
				<a href="#cancelEditing" id="cancel_order_edit" class="cancel">{t _cancel_editing_order_info}</a>
			</li>
		</ul>
	</fieldset>

	<div class="tabContainer">
		<ul class="tabList tabs">
			<li id="tabOrderInfo" class="tab active">
				<a href="{link controller=backend.customerOrder action=info id=_id_}"}">{t _order_info}</a>
				<span class="tabHelp">orders.edit</span>
			</li>
			<li id="tabOrderPayments" class="tab active">
				<a href="{link controller=backend.payment id=_id_}"}">{t _order_payments}</a>
				<span class="tabHelp">orders.edit</span>
			</li>
			<li id="tabOrderCommunication" class="tab active">
				<a href="{link controller=backend.orderNote id=_id_}"}">{t _order_communication}</a>
				<span class="tabHelp">orders.edit</span>
			</li>
			<li id="tabOrderLog" class="tab active">
				<a href="{link controller=backend.orderLog id=_id_}"}">{t _order_log}</a>
				<span class="tabHelp">orders.edit</span>
			</li>
			<li id="tabPreviousOrders" class="tab active">
				<a href="{link controller=backend.customerOrder action=orders query="userOrderID=_id_"}"}">{t _previous_orders}</a>
				<span class="tabHelp">orders.edit</span>
			</li>
		</ul>
	</div>
	<fieldset class="sectionContainer maxHeight h--50"></fieldset>


	{literal}
	<script type="text/javascript">
		Event.observe($("cancel_order_edit"), "click", function(e) {
			Event.stop(e);
			var order = Backend.CustomerOrder.Editor.prototype.getInstance(Backend.CustomerOrder.Editor.prototype.getCurrentId(), false);
			order.cancelForm();
		});
	</script>
	{/literal}
</fieldset>