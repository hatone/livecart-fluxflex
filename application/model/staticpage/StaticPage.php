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

ClassLoader::import('application.model.eav.EavAble');
ClassLoader::import('application.model.eav.EavObject');
ClassLoader::importNow('application.helper.CreateHandleString');


/**
 * Static site pages (shipping information, contact information, terms of service, etc.)
 *
 * @package application.model.staticpage
 * @author Integry Systems <http://integry.com>
 */
class StaticPage extends MultilingualObject implements EavAble
{
	private $isFileLoaded = false;

	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);
		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("parentID", "StaticPage", "ID", null, ARInteger::instance()));
		$schema->registerField(new ARField("handle", ARVarchar::instance(255)));
		$schema->registerField(new ARField("handle", ARVarchar::instance(255)));
		$schema->registerField(new ARField("title", ARArray::instance()));
		$schema->registerField(new ARField("text", ARArray::instance()));
		$schema->registerField(new ARField("metaDescription", ARArray::instance()));
		$schema->registerField(new ARField("menu", ARArray::instance()));
		$schema->registerField(new ARField("position", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("eavObjectID", "eavObject", "ID", 'EavObject', ARInteger::instance()), false);
	}

	/*####################  Static method implementations ####################*/

	/**
	 * Gets an existing record instance (persisted on a database).
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 * @param array $data	Record data array (may include referenced record data)
	 *
	 * @return StaticPage
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false, $data = array())
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords, $data);
	}

	public static function getNewInstance()
	{
		return parent::getNewInstance(__CLASS__);
	}

	public static function getInstanceByHandle($handle)
	{
		$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle(__CLASS__, 'handle'), $handle));
		$s = self::getRecordSet(__CLASS__, $f);

		if (!$s->size())
		{
			throw new ARNotFoundException(__CLASS__, 'handle: ' . $handle);
		}

		return $s->get(0);
	}

	public static function getIsInformationMenuCondition()
	{
		return new LikeCond(new ARFieldHandle('StaticPage', 'menu'), '%"INFORMATION";b:1%');
	}

	public static function getIsRootCategoriesMenuCondition()
	{
		return new LikeCond(new ARFieldHandle('StaticPage', 'menu'), '%"ROOT_CATEGORIES";b:1%');
	}


	public function getFileName()
	{
		return ClassLoader::getRealPath('cache.staticpage') . '/' . $this->getID() . '.php';
	}

	public function save($forceOperation = null)
	{
		$this->loadFile();

		if (!$this->handle->get())
		{
			$this->handle->set(createHandleString($this->getValueByLang('title')));
		}

		parent::save($forceOperation);

		$this->saveFile();
	}

	public function delete()
	{
		@unlink($this->getFileName());
		return parent::delete();
	}

	public function toArray()
	{
		$array = parent::toArray();
		if (!$this->isLoaded())
		{
			$this->loadFile();
			$lang = self::getApplication()->getLocaleCode();
			$array['title_lang'] = $this->getValueByLang('title', $lang);
			$array['text_lang'] = $this->getValueByLang('text', $lang);
		}

		// when the instance is not loaded
		if (!$array['handle'])
		{
			$array['handle'] = $this->handle->get();
		}

		$array['menuInformation'] = false;
		$array['menuRootCategories'] = false;

		if (array_key_exists('menuData', $array))
		{
			if (array_key_exists('INFORMATION', $array['menuData']))
			{
				$array['menuInformation'] = (bool)$array['menuData']['INFORMATION'];
			}
			if (array_key_exists('ROOT_CATEGORIES', $array['menuData']))
			{
				$array['menuRootCategories'] = (bool)$array['menuData']['ROOT_CATEGORIES'];
			}
		}
		return $array;
	}

	public function getSubPageArray()
	{
		$f = select();
		$f->setOrder(f('StaticPage.position'));
		return $this->getRelatedRecordSetArray('StaticPage', $f);
	}

	public static function createTree(array $pages)
	{
		$index = array();
		foreach ($pages as &$page)
		{
			$index[$page['ID']] =& $page;
		}

		foreach ($pages as $key => &$page)
		{
			if ($page['parentID'] && isset($index[$page['parentID']]))
			{
				$index[$page['parentID']]['children'][] =& $page;
				unset($pages[$key]);
			}
		}

		return $pages;
	}

	protected function insert()
	{
	  	// get max position
	  	$f = new ARSelectFilter();
	  	$f->setOrder(new ARFieldHandle('StaticPage', 'position'), 'DESC');
	  	$f->setLimit(1);
	  	$rec = ActiveRecord::getRecordSetArray('StaticPage', $f);
		$position = (is_array($rec) && count($rec) > 0) ? $rec[0]['position'] + 1 : 1;

		$this->position->set($position);

		return parent::insert();
	}

	private function saveFile()
	{
		$fileData = array('handle' => $this->handle->get(),
						  'title' => $this->title->get(),
						 );

		$dir = dirname($this->getFileName());

		if (!file_exists($dir))
		{
			mkdir($dir, 0777, true);
		}

		file_put_contents($this->getFileName(), '<?php $pageData = ' . var_export($fileData, true) . '; ?>');
	}

	private function loadFile()
	{
		if (!$this->isLoaded() && !$this->isFileLoaded && file_exists($this->getFileName()))
		{
			if (!file_exists($this->getFileName()))
			{
				$this->load();
				$this->saveFile();
			}

			include $this->getFileName();
			$this->title->set($pageData['title']);

			if (!$this->handle->get())
			{
				$this->handle->set($pageData['handle']);
			}

			$this->isFileLoaded = true;
		}
	}
}

?>
