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

/**
 * Search site news
 *
 * @package application.model.searchable
 * @author Integry Systems
 */
class SearchableNewsPost extends SearchableModel
{
	public function getClassName()
	{
		return 'NewsPost';
	}

	public function loadClass()
	{
		ClassLoader::import('application.model.sitenews.NewsPost');
	}

	public function getSelectFilter($searchTerm)
	{
		$c = new ARExpressionHandle($this->getWeighedSearchCondition
			(
				$this->getOption('BACKEND_QUICK_SEARCH')
					? array('title' => 1)
					: array('title' => 4, 'text' => 2, 'moreText' => 1),
				$searchTerm
			));
		$f = new ARSelectFilter(new MoreThanCond($c, 0));
		$f->setOrder($c, 'DESC');
		return $f;
	}
}

?>
