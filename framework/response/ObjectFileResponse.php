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

ClassLoader::import('framework.response.Response');

/**
 * JSON response
 *
 * @package framework.response
 * @author	Integry Systems
 */
class ObjectFileResponse extends Response
{
	private $file;
	private $deleteFile = false;

	public function __construct(ObjectFile $objectFile)
	{
		if ($objectFile->isLocalFile())
		{
			$this->file = $objectFile;

			$this->setHeader('Cache-Control', 'no-cache, must-revalidate');
			$this->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
			$this->setHeader('Content-type', $objectFile->getMimeType());
			$this->setHeader('Content-Disposition', 'attachment; filename="'.$objectFile->getBaseName().'"');
			$this->setHeader('Content-Length', (string)$objectFile->getSize());
			$this->setHeader('Content-Encoding', 'none');
		}
		else
		{
			$this->setHeader('Location', $objectFile->filePath->get());
		}
	}

	public function getFile()
	{
		return $this->file;
	}

	public function deleteFileOnComplete($delete = true)
	{
		$this->deleteFile = $delete;
	}

	public function sendData()
	{
		@ini_set('max_execution_time', 0);
		$f = fopen($this->file->getPath(), 'r');

		while ($f && !feof($f))
		{
			echo fread($f, 4096);
			flush();
			ob_flush();
		}

		fclose($f);
	}

	public function getData()
	{
		return '';
	}

	public function __destruct()
	{
		if ($this->deleteFile)
		{
			$path = $this->file->getPath();
			if (file_exists($path))
			{
				unlink($path);
			}
		}
	}
}

?>
