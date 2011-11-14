<div id="reviews">

	{foreach from=$reviews item=review name=reviews}
		<div class="review{if $smarty.foreach.reviews.first} first{/if}">
			{include file="product/ratingBreakdown.tpl" ratings=$review.ratings}
			<span class="reviewRating">{include file="product/ratingImage.tpl" rating=$review.rating}</span>
			<span class="reviewNickname">{$review.nickname}</span>, <span class="reviewDate">{$review.formatted_dateCreated.date_long}</span>
			<div class="reviewTitle">
				{$review.title}
			</div>
			<p class="reviewText">
				{$review.text|@nl2br}
			</p>
		</div>
	{/foreach}

</div>