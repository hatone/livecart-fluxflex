<?php
/*************************************************************************************************
 * LiveCart																					  *
 * Copyright (C) 2007-2009 UAB "Integry Systems" (http://livecart.com)							*
 * All rights reserved																		   *
 *																							   *
 * This source file is a part of LiveCart software package and is protected by LiveCart license. *
 * The license text can be found in the license.txt file. In case you received a package without *
 * a license file, the license text is also available online at http://livecart.com/license	  *
 *************************************************************************************************/

ClassLoader::import('application.model.searchable.SearchableModel');
ClassLoader::import('application.model.searchable.index.SearchableConfigurationIndexing');

/**
 * Search static pages
 *
 * @package application.model.searchable
 * @author Integry Systems
 */
class SearchableConfiguration extends SearchableModel
{
	public function getClassName()
	{
		return 'SearchableItem';
	}

	public function loadClass()
	{
		ClassLoader::import('application.model.searchable.item.SearchableItem');
		ClassLoader::import('application.model.searchable.index.SearchableConfigurationIndexing');
		SearchableConfigurationIndexing::buildIndexIfNeeded();
	}

	public function getSelectFilter($searchTerm)
	{
		// create initial index
		if (0 && !SearchableItem::getRecordCount())
		{
			$app = ActiveRecordModel::getApplication();
			$sc = new SearchableConfigurationIndexing($app->getConfig(), $app);
			$sc->buildIndex(null);
		}

		$c = new ARExpressionHandle($this->getWeighedSearchCondition(array('value' => 1), $searchTerm));
		$app = ActiveRecordModel::getApplication();
		$f = new ARSelectFilter(new MoreThanCond($c, 0));

		$f->mergeCondition(
			new OrChainCondition(
				array
				(
					eq(f('SearchableItem.locale'), $app->getDefaultLanguageCode()),
					eq(f('SearchableItem.locale'), $app->getLocaleCode()),
					isnull(f('SearchableItem.locale'))
				)
			)
		);
		$f->setOrder(f('SearchableItem.sort'), 'DESC');
		$f->setOrder($c, 'DESC');
		return $f;
	}

	public function isFrontend()
	{
		return false;
	}
}

?>
