<div>

{activeGrid
	prefix="reviews"
	id=$id
	role="product.mass"
	controller="backend.review" action="lists"
	displayedColumns=$displayedColumns
	availableColumns=$availableColumns
	totalCount=$totalCount
	filters=$filters
	container=$container
	dataFormatter="Backend.Review.GridFormatter"
	count="backend/review/count.tpl"
	massAction="backend/review/massAction.tpl"
}

</div>

{literal}
<script type="text/javascript">
{/literal}
	var massHandler = new ActiveGrid.MassActionHandler(
						$('reviewMass_{$id}'),
						window.activeGrids['reviews_{$id}'],
{literal}
						{
							'onComplete':
								function()
								{
								}
						}
{/literal}
						);
	massHandler.deleteConfirmMessage = '{t _delete_conf|addslashes}' ;
	massHandler.nothingSelectedMessage = '{t _nothing_selected|addslashes}' ;

{literal}
</script>
{/literal}