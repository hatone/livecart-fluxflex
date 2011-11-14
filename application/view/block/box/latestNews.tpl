<div class="box latestNews">
	<div class="title"><div>{t _latest_news}</div></div>

	<div class="content">
		<ul class="latestNewsSide">
			{foreach from=$news item=entry name=news}
				{if !$smarty.foreach.news.last || !$isNewsArchive}
					<li>
						<a href="{newsUrl news=$entry}">{$entry.title_lang}</a>
						<span class="date">{$entry.formatted_time.date_medium}</span>
					</li>
				{else}
					<div class="newsArchive">
						<a href="{link controller=news}">{t _news_archive}</a>
					</div>
				{/if}
			{/foreach}
		</ul>
	</div>
</div>