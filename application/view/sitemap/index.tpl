<?xml version='1.0' encoding='UTF-8'?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
	http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd">
{foreach from=$maps item=map}
	<sitemap>
{foreach from=$map key=nodename item=node}
		<{$nodename}>{$node}</{$nodename}>
{/foreach}
	</sitemap>
{/foreach}
</sitemapindex>