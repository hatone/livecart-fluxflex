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

ClassLoader::import('application.model.datasync.DataExport');

/**
 * Handle data export in CSV format
 *
 * @package application.model
 * @author Integry Systems <http://integry.com>
 */
class CsvExport extends DataExport
{
	public function __construct(ExportProfile $profile, $file, $append = false)
	{
		$this->profile = $profile;

		if (is_resource($file))
		{
			$this->file = $file;
		}
		else
		{
			$this->file = fopen($file, $append ? 'a' : 'w');
		}
	}

	protected function writeData($data)
	{
		fputcsv($this->file, $data);
	}

	public function close()
	{
		fclose($this->file);
	}
}

?>
