{if $tree}
	{if $tree.ID}<item child="{$tree.childrenCount}" id="{$tree.ID}" text="{$tree.name|escape:'html'}" {if !$doNotTouch && $tree.ID == $targetID}selected="true" call="true"{/if}>{/if}
		{foreach from=$tree.children key="name" item="subtree"}
			  {include file="backend/selectFile/xmlNodes.tpl" tree=$subtree}
		{/foreach}
	{if $tree.ID}</item>{/if}
{/if}
