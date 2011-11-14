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

ClassLoader::import("application.model.ActiveRecordModel");

/**
 * Searchable item entry
 *
 * @package application.model.searchable
 * @author Integry Systems <http://integry.com>
 */
class SearchableItem extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName(__CLASS__);
		$schema->registerField(new ARField("section", ARVarchar::instance(64)));
		$schema->registerField(new ARField("value", ARText::instance() ));
		$schema->registerField(new ARField("locale", ARVarchar::instance(2)));
		$schema->registerField(new ARField("meta", ARText::instance()));
		$schema->registerField(new ARField("sort", ARInteger::instance()));

		// $schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		// $schema->registerField(new ARField("type", ARVarchar::instance(16)));
	}

	public static function getRecordCount($locale=null)
	{
		$filter = new ARSelectFilter();
		if ($locale)
		{
			$filter->mergeCondition(eq(f(__CLASS__.'.locale'), $locale));
		}
		return ActiveRecordModel::getRecordCount(__CLASS__, $filter);
	}

	public static function bulkClearIndex($section)
	{
		ActiveRecordModel::executeUpdate('DELETE FROM '.__CLASS__.(
			$section
				? ' WHERE section=0x'.bin2hex($section)
				:''
			)
		); // and type = ..
	}

	public static function bulkAddIndex($list)
	{
		$chunks = array();
		while($item = array_pop($list))
		{
			$locale = array_key_exists('locale', $item['meta']) ? $item['meta']['locale'] : null;
			if($locale && strlen($locale))
			{
				$locale = '0x'.bin2hex($locale);
			}
			else
			{
				$locale = 'NULL';
			}
			$chunks[] = sprintf('(%s, %s, %s, %s, %s)',
				'0x'.bin2hex($item['value']),
				$locale,
				array_key_exists('section', $item) ? '0x'.bin2hex($item['section']) : 'NULL',
				'0x'.bin2hex(serialize($item['meta'])),
				array_key_exists('sort', $item) && is_numeric($item['sort']) ? intval($item['sort'], 10) : 'NULL'
			);
		}

		ActiveRecordModel::executeUpdate('INSERT INTO '.__CLASS__.'(value, locale, section, meta, sort) VALUES '.implode(',',$chunks));
	}
}

?>
