{capture}
	{form handle=$productForm}
	{capture assign="specField"}
		{include file="backend/product/form/specFieldList.tpl"}
	{/capture}
	{/form}
{/capture}

{ldelim} 'status': 'success', 'message': '{t _product_information_was_successfully_saved|addslashes}', 'specFieldHtml': {json array=$specField}{rdelim}