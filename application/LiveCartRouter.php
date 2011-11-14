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

ClassLoader::import('framework.request.Router');

/**
 *  Implements LiveCart-specific routing logic
 *
 *  @package application
 *  @author Integry Systems
 */
class LiveCartRouter extends Router
{
	private $langReplaces = array();

	private $langFields = array();

	private $langIDs = array();

	public function createUrlFromRoute($route, $isXHtml = false)
	{
		preg_match('/^([a-z]{2})\//', $route, $match);
		$varSuffix = isset($match[1]) ? '_' . $match[1] : '';

		foreach ($this->langReplaces as $needle => $replace)
		{
			$repl = !empty($replace['record'][$replace['field'] . $varSuffix]) ? $replace['record'][$replace['field'] . $varSuffix] : $replace['record'][$replace['field']];
			if ($repl)
			{
				$repl = createHandleString($repl);
				if ($repl != $needle)
				{
					$route = str_replace($needle, $repl, $route);
				}
			}
		}

		return parent::createUrlFromRoute($route, $isXHtml);
	}

	public function setLangReplace($value, $field, $record)
	{
		$this->langReplaces[$value] = array('field' => $field, 'record' => array_intersect_key($record, $this->getLangFields($field)));
	}

	private function getLanguageIDs()
	{
		if (!$this->langIDs)
		{
			$this->langIDs = ActiveRecordModel::getApplication()->getLanguageArray(true);
		}

		return $this->langIDs;
	}

	private function getLangFields($field)
	{
		if (!isset($this->langFields[$field]))
		{
			$this->langFields[$field] = array();
			foreach ($this->getLanguageIDs() as $id)
			{
				$this->langFields[$field][$field . '_' . $id] = true;
			}
		}

		return $this->langFields[$field];
	}
}

?>
