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

ClassLoader::import("application.controller.backend.abstract.StoreManagementController");
ClassLoader::import('library.import.LiveCartImporter');

/**
 * Import data from other shopping cart programs
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role dbmigration
 */
class DatabaseImportController extends StoreManagementController
{
	public function index()
	{
		$response = new ActionResponse();
		$response->set('form', $this->getForm());
		$response->set('carts', $this->getDrivers());
		$response->set('dbTypes', array('mysql' => 'MySQL'));
		$response->set('recordTypes', LiveCartImporter::getRecordTypes());
		return $response;
	}

	public function import()
	{
		//ignore_user_abort(true);
		set_time_limit(0);

		$validator = $this->buildValidator();
		if (!$validator->isValid())
		{
			return new JSONResponse(array('errors' => $validator->getErrorList()));
		}

		$dsn = $this->request->get('dbType') . '://' .
				   $this->request->get('dbUser') .
				   		($this->request->get('dbPass') ? ':' . $this->request->get('dbPass') : '') .
				   			'@' . $this->request->get('dbServer') .
				   				'/' . $this->request->get('dbName');

		try
		{
			$cart = $this->request->get('cart');
			ClassLoader::import('library.import.driver.' . $cart);
			$driver = new $cart($dsn, $this->request->get('filePath'));
		}
		catch (SQLException $e)
		{
			$validator->triggerError('dbServer', $e->getNativeError());
			$validator->saveState();
			return new JSONResponse(array('errors' => $validator->getErrorList()));
		}

		if (!$driver->isDatabaseValid())
		{
			$validator->triggerError('dbName', $this->maketext('_invalid_database', $driver->getName()));
			$validator->saveState();
			return new JSONResponse(array('errors' => $validator->getErrorList()));
		}

		if (!$driver->isPathValid())
		{
			$validator->triggerError('filePath', $this->maketext('_invalid_path', $driver->getName()));
			$validator->saveState();
			return new JSONResponse(array('errors' => $validator->getErrorList()));
		}

		$importer = new LiveCartImporter($driver);

		$response = new JSONResponse(null);

		// get importable data types
		$response->flush($this->getResponse(array('types' => $importer->getItemTypes())));

		ActiveRecord::beginTransaction();

		// process import
		try
		{
			while (true)
			{
				$result = $importer->process();

				$response->flush($this->getResponse($result));

	//echo '|' . round(memory_get_usage() / (1024*1024), 1) . " ($result[type] : " . array_shift(array_shift(ActiveRecord::getDataBySQL("SELECT COUNT(*) FROM " . $result['type']))) . ")<br> \n";

				if (is_null($result))
				{
					break;
				}
			}
		}
		catch (Exception $e)
		{
			print_r($e->getMessage());
			ActiveRecord::rollback();
		}

		if (!$this->application->isDevMode() || 1)
		{
			ActiveRecord::commit();
		}
		else
		{
			ActiveRecord::rollback();
		}

		$importer->reset();

		return $response;
	}

	private function getResponse($data)
	{
		return '|' . base64_encode(json_encode($data));
	}

	private function getDrivers()
	{
		$drivers = array();

		foreach (new DirectoryIterator(ClassLoader::getRealPath('library.import.driver')) as $file)
		{
			if (!$file->isDot())
			{
				include_once $file->getPathname();
				$className = basename($file->getFileName(), '.php');
				$drivers[$className] = call_user_func(array($className, 'getName'));
			}
		}

		natcasesort($drivers);

		return $drivers;
	}

	private function getForm()
	{
		return new Form($this->buildValidator());
	}

	private function buildValidator()
	{
		ClassLoader::import('application.helper.filter.HandleFilter');

		$val = $this->getValidator('databaseImport', $this->request);
		$val->addCheck('cart', new IsNotEmptyCheck($this->translate('_err_no_cart_selected')));
		$val->addCheck('dbServer', new IsNotEmptyCheck($this->translate('_err_no_database_server')));
		$val->addCheck('dbType', new IsNotEmptyCheck($this->translate('_err_no_db_type')));
		$val->addCheck('dbName', new IsNotEmptyCheck($this->translate('_err_no_database_name')));
		$val->addCheck('dbUser', new IsNotEmptyCheck($this->translate('_err_no_database_username')));

		return $val;
	}
}

?>
