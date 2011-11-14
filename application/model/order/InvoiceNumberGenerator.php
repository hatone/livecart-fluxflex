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

ClassLoader::import("application.model.order.CustomerOrder");

/**
 * Defines abstract interface for invoice ID number generator classes
 *
 * @package application.model.order
 * @author Integry Systems <http://integry.com>
 */
abstract class InvoiceNumberGenerator
{
	protected $order;

	public function __construct(CustomerOrder $order)
	{
		$this->order = $order;
	}

	public static function getGenerator(CustomerOrder $order)
	{
		$class = ActiveRecordModel::getApplication()->getConfig()->get('INVOICE_NUMBER_GENERATOR');
		self::loadGeneratorClass($class);
		ClassLoader::import('application.model.order.invoiceNumber.' . $class);

		return new $class($order);
	}

	public static function getGeneratorClasses()
	{
		$files = self::getGeneratorFiles();
		unset($files['SequentialInvoiceNumber']);

		$classes = array_keys($files);
		array_unshift($classes, 'SequentialInvoiceNumber');

		return $classes;
	}

	private static function loadGeneratorClass($class)
	{
		$files = self::getGeneratorFiles();
		if (isset($files[$class]))
		{
			include_once $files[$class];
		}
	}

	private static function getGeneratorFiles()
	{
		$files = array();
		foreach (ActiveRecordModel::getApplication()->getConfigContainer()->getDirectoriesByMountPath('application.model.order.invoiceNumber') as $dir)
		{
			foreach (glob($dir . '/*.php') as $file)
			{
				$files[basename($file, '.php')] = $file;
			}
		}

		return $files;
	}

	public abstract function getNumber();
}

?>
