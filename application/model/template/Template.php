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

/**
 * Page template file logic - saving and retrieving template code.
 *
 * There are two sets of template files active at the same time:
 *
 *		1) application.view - default view template files
 *		2) storage.customize - edited template files.
 *
 * This system allows to modify template files without overwriting the existing ones, among other benefits.
 *
 * @package application.model.template
 * @author Integry Systems <http://integry.com>
 */
class Template
{
	protected $code;

	protected $file;

	protected $theme;

	protected $version;

	const BACKUP_FILE_COUNT = 10;

	public function __construct($fileName, $theme = null, $version = null)
	{
		$this->theme = $theme;
		$this->version = is_numeric($version) && $version > 0 ? (int)$version : null;

		// do not allow to leave view template directory by prefixing ../
		$fileName = preg_replace('/^[\\\.\/]+/', '', $fileName);
		if ($this->theme)
		{
			if (substr($fileName, 0, 6) == 'theme/')
			{
				$parts = explode('/', $fileName, 3);
				$fileName = $parts[2];
			}

			$fileName = 'theme/' . $this->theme . '/' . $fileName;
		}
		$path = self::getRealFilePath($fileName);
		if (file_exists($path) && $this->version == null /* working with current file version */)
		{
			$this->code = file_get_contents($path);
		}

		$this->file = str_replace('\\', '/', $fileName);

		if ($this->version)
		{
			$this->code = $this->readBackup();
		}
	}

	public static function getTree($dir = null, $root = null, $idPrefix = '')
	{
	  	if (!$dir)
	  	{
			$dir = ClassLoader::getRealPath('application.view.');

			// get user created template files
			$customFiles = self::getTree(ClassLoader::getRealPath('storage.customize.view.'));
		}

		if (!file_exists(realpath($dir)))
		{
			return array();
		}

		$dir = realpath($dir) . DIRECTORY_SEPARATOR;

		if (!$root)
		{
			$root = $dir;
		}

		if (!file_exists($dir))
		{
			return array();
		}

		$rootLn = strlen($root);

		$res = array();
		$d = new DirectoryIterator($dir);

		foreach ($d as $file)
		{
			if (!$file->isDot())
			{
				$id = substr($file->getPathName(), $rootLn);

				if ($file->isDir())
				{
					$dir = self::getTree($file->getPathName(), $root, $idPrefix);
					if ($dir)
					{
						$res[$file->getFileName()]['id'] = $idPrefix . $id;
						$res[$file->getFileName()]['subs'] = $dir;
					}
				}
				else //if (substr($file->getFileName(), -4) == '.tpl')
				{
					$res[$file->getFileName()]['id'] = $idPrefix . $id;
					$res[$file->getFileName()]['isCustom'] = !file_exists(self::getOriginalFilePath($id));

					if (substr($id, 0, 7) == 'module/')
					{
						$res[$file->getFileName()]['isCustom'] = false;
					}
				}
			}
		}

		if (isset($customFiles))
		{
			$res = self::array_merge_rec($res, $customFiles);
		}

		uasort($res, array('Template', 'sortTree'));

		return $res;
	}

	public static function getRealFilePath($fileName)
	{
		return ActiveRecordModel::getApplication()->getRenderer()->getTemplatePath($fileName);
	}

	public static function getOriginalFilePath($fileName)
	{
		return ClassLoader::getRealPath('application.view.') . $fileName;
	}

	public static function getCustomizedFilePath($fileName)
	{
		return ClassLoader::getRealPath('storage.customize.view.') . $fileName;
	}

	public function setCode($code)
	{
		$this->code = $code;
	}

	public function getCode()
	{
	 	return $this->code;
	}

	public function getFileName()
	{
		return $this->file;
	}

	public function sortTree($a, $b)
	{
		$ap = (isset($a['subs']) * 10) + strnatcasecmp($b['id'], $a['id']);
		$bp = (isset($b['subs']) * 10) + strnatcasecmp($a['id'], $b['id']);

		return $ap > $bp ? -1 : ($ap == $bp) ? 0 : 1;
	}

	private function checkForChanges()
	{
		$l = str_replace("\r\n", "\n", $this->getContent(self::getCustomizedFilePath($this->file)));
		$r = str_replace("\r\n", "\n", $this->getContent(self::getOriginalFilePath($this->file)));

		if ($l == $r)
		{
			$this->restoreOriginal();
		}
	}

	private function getContent($file)
	{
		if (file_exists($file))
		{
			return file_get_contents($file);
		}
	}

	public function save()
	{
		$path = self::getCustomizedFilePath($this->file);

		$dir = dirname($path);
		if (!is_dir($dir))
		{
			mkdir($dir, 0777, true);
			chmod($dir, 0777);
		}

		$res = file_put_contents($path, $this->code);

		$this->checkForChanges();

		if ($res)
		{
			$this->backup();
		}
		return $res !== false;
	}


	public function restoreOriginal()
	{
		$path = self::getCustomizedFilePath($this->file);
		if (!file_exists($path))
		{
			return true;
		}

		$cacheDir = ClassLoader::getRealPath('cache.templates_c.customize');
		if (is_dir($cacheDir))
		{
			foreach (new DirectoryIterator($cacheDir) as $file)
			{
				if (!$file->isDot())
				{
					unlink($file->getPathname());
				}
			}
		}

		return unlink($path);
	}

	/**
	 * deletes customized template
	 *  removes:
	 *   customized template file 
	 *   customized template file for each theme (if exists)
	 *   backups (for customized template file, and each theme)
	 */
	public function delete()
	{
		if ($this->isCustomFile() == false)
		{
			throw new Exception('Only custom files can be deleted');
		}

		$filesToRemove[] = Template::getCustomizedFilePath($this->file);
		$filesToRemove = array_merge($filesToRemove, $this->getBackups(false));

		$otherThemes = $this->getOtherThemes();
		foreach($otherThemes as $key=>$themename)
		{
			if($key == '')
			{
				continue;
			}
			$template = new Template($this->file, $themename);
			$filesToRemove[] = Template::getCustomizedFilePath($template->getFileName());
			$filesToRemove = array_merge($filesToRemove, $template->getBackups(false));
		}

		$filesToRemove = array_values($filesToRemove);

		while($fn = array_pop($filesToRemove))
		{
			if(is_readable($fn))
			{
				unlink($fn);
			}
		}

		return true;
	}

	public function isCustomFile()
	{
		return !file_exists(self::getOriginalFilePath($this->file)) && (substr($this->file, 0, 7) != 'module/');
	}

	public function toArray()
	{
		$array = array();
		$array['code'] = $this->code;
		$array['file'] = $this->file;
		$array['isCustomized'] = file_exists(self::getCustomizedFilePath($this->file));
		$array['isCustomFile'] = $this->isCustomFile();
		$array['backups'] = $this->getBackups();
		$array['version'] = $this->version;
		$array['otherThemes'] = $this->getOtherThemes();
		$array['theme'] = $this->theme;

		return $array;
	}

	private function getOtherThemes()
	{
		$fileName = $this->file;
		if ($this->theme)
		{
			// chop off theme/<themename> from $fileName
			$fileName = substr($fileName, strlen('theme'.DIRECTORY_SEPARATOR.$this->theme)+1);
		}
		$application = ActiveRecordModel::getApplication();
		$result = array('' => $application->translate('_other_themes'));
		foreach($application->getRenderer()->getThemeList() as $themename)
		{
			$fn = ClassLoader::getRealPath('storage.customize.view.theme.'.$themename.'.'). $fileName;
			if(file_exists($fn))
			{
				$result[$themename] = $themename;
			}
		}
		return $result;
	}
	
	private function getBackups($prettyBackupNames=true)
	{
		$application = ActiveRecordModel::getApplication();
		$locale = $application->getLocale();
		$result = array();
		$path = ClassLoader::getRealPath('storage.backup.template.').$this->file.DIRECTORY_SEPARATOR;
		foreach(glob($path.'*') as $file)
		{
			if(preg_match('/(\d{4}\-\d{2}\-\d{2}\-\d{2}\-\d{2}\-\d{2})$/', $file, $z)) // is this a backup file?
			{
				if ($prettyBackupNames)
				{
					list($y, $m, $d, $h, $min, $s) = explode('-',$z[1]);
					$ts = strtotime($y.'-'.$m.'-'.$d.' '.$h.':'.$min.':'.$s);
					$formatted = $locale->getFormattedTime($ts);
					$result[$ts] = $formatted['date_medium'].' '.$formatted['time_short'];
				}
				else
				{
					$result[$z[1]] = $file;
				}
			}
		}
		ksort($result);
		if ($prettyBackupNames)
		{
			$result[-1] = $application->translate('_previous_file_versions');
		}
		return array_reverse($result, true);
	}

	private function readBackup()
	{
		$path = ClassLoader::getRealPath('storage.backup.template.').$this->file.DIRECTORY_SEPARATOR.date('Y-m-d-H-i-s', $this->version);
		return file_exists($path) ? file_get_contents($path) : false;
	}

	private function backup()
	{
		$path = ClassLoader::getRealPath('storage.backup.template.').$this->file.DIRECTORY_SEPARATOR.date('Y-m-d-H-i-s');
		$dir = dirname($path);
		if(is_dir($dir) == false)
		{
			mkdir($dir, 0777, true);
			chmod($dir, 0777);
		}
		$res = file_put_contents($path, $this->code);
		$backups = $this->getBackups(false);
		$c = count($backups);
		if ($c > self::BACKUP_FILE_COUNT)
		{
			$backupBase = ClassLoader::getRealPath('storage.backup.template.').$this->file.DIRECTORY_SEPARATOR;
			$keys = array_keys($backups);
			for ($i= self::BACKUP_FILE_COUNT; $i<$c; $i++)
			{
				if (strpos($backups[$keys[$i]], $backupBase) === 0 && is_readable($backups[$keys[$i]]))
				{
					unlink($backups[$keys[$i]]);
				}
			}
		}
	}

	private function array_merge_rec($array1, $array2)
	{
		$arrays = func_get_args();
		$narrays = count($arrays);

		// check arguments
		// comment out if more performance is necessary (in this case the foreach loop will trigger a warning if the argument is not an array)
		for ($i = 0; $i < $narrays; $i ++) {
			if (!is_array($arrays[$i])) {
				// also array_merge_recursive returns nothing in this case
				trigger_error('Argument #' . ($i+1) . ' is not an array - trying to merge array with scalar! Returning null!', E_USER_WARNING);
				return;
			}
		}

		// the first array is in the output set in every case
		$ret = $arrays[0];

		// merege $ret with the remaining arrays
		for ($i = 1; $i < $narrays; $i ++) {
			foreach ($arrays[$i] as $key => $value) {
				if (((string) $key) === ((string) intval($key))) { // integer or string as integer key - append
					$ret[] = $value;
				}
				else { // string key - megre
					if (is_array($value) && isset($ret[$key])) {
						// if $ret[$key] is not an array you try to merge an scalar value with an array - the result is not defined (incompatible arrays)
						// in this case the call will trigger an E_USER_WARNING and the $ret[$key] will be null.
						$ret[$key] = self::array_merge_rec($ret[$key], $value);
					}
					else {
						$ret[$key] = $value;
					}
				}
			}
		}

		return $ret;
}
}

?>
