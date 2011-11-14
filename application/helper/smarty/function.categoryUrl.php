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

ClassLoader::importNow('application.helper.CreateHandleString');

/**
 * Generates category page URL
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty
 * @author Integry Systems
 */
function smarty_function_categoryUrl($params, LiveCartSmarty $smarty)
{
	return createCategoryUrl($params, $smarty->getApplication());
}

function createCategoryUrl($params, LiveCart $application)
{
	static $simpleUrlTemplate = null;

	// create URL template
	$router = $application->getRouter();
	if (!$simpleUrlTemplate)
	{
		$simpleUrlTemplate = $router->createUrl(array('controller' => 'category',
								 'action' => 'index',
								 'cathandle' => '---',
								 'id' => 999,
							));
		$simpleUrlTemplate = strtr($simpleUrlTemplate, array(999 => '#', '---' => '|'));
	}

	$category = $params['data'];

	if (!isset($category['ID']))
	{
		$category['ID'] = 1;
	}

	$handle = isset($category['name_lang']) ? createHandleString($category['name_lang']) : '';

	// category names to use in other language links
	$router->setLangReplace($handle, 'name', $category);

	// no extra params, so we don't need to call the router to build the URL
	if (count($params) == 1)
	{
		return strtr($simpleUrlTemplate, array('#' => $category['ID'], '|' => $handle));
	}

	$filters = array();

	// remove filter (expand search)
	if (isset($params['removeFilter']))
	{
	  	foreach ($params['filters'] as $key => $filter)
	  	{
			if ($filter['ID'] == $params['removeFilter']['ID'])
			{
				unset($params['filters'][$key]);
			}
		}
	}

	if (isset($params['removeFilters']))
	{
		foreach ($params['removeFilters'] as $filter)
		{
			unset($params['filters'][$filter['ID']]);
		}
	}

	// get filters
	if (isset($params['filters']))
	{
		foreach ($params['filters'] as $filter)
		{
		  	$filters[] = filterHandle($filter);
		}
	}

	// apply new filter (narrow search)
	if (!empty($params['addFilter']))
	{
	  	$filters[] = filterHandle($params['addFilter']);
	}

	if (empty($handle))
	{
		$handle = '-';
	}

	$urlParams = array('controller' => 'category',
					   'action' => empty($params['action']) ? 'index' : $params['action'],
					   'cathandle' => $handle,
					   'id' => $category['ID'],
					   );

	if (!empty($params['query']))
	{
		$urlParams['query'] = $params['query'];
	}

	if (!empty($params['page']))
	{
		$urlParams['page'] = $params['page'];
	}

	if ($filters)
	{
		$urlParams['filters'] = implode(',', $filters);
	}

	if ('index' != $urlParams['action'])
	{
		unset($urlParams['cathandle']);
		if (isset($urlParams['filters']))
		{
			$urlParams['query'] = 'filters=' . $urlParams['filters'];
			unset($urlParams['filters']);
		}
	}

	$url = $application->getRouter()->createUrl($urlParams, true);
	if (!empty($params['full']))
	{
		$url = $application->getRouter()->createFullUrl($url);
	}

	// remove empty search query parameter
	return preg_replace('/[\?&]q=$/', '', $url);
}

function filterHandle($filter)
{
	if (is_object($filter))
	{
		$filter = $filter->toArray();
	}

	return (isset($filter['FilterGroup']) ? $filter['FilterGroup']['SpecField']['handle'] . '.' : '') . $filter['handle'] . '-' . $filter['ID'];
}

?>
