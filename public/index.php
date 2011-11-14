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
	 * LiveCart front controller
	 *
	 * @author Integry Systems
	 * @package application
	 */

	// @todo: remove
	/*
	function onShutDown()
	{
		define('SHUTDOWN', true);
	}

	function logDestruct($obj, $details = '')
	{
		if (defined('SHUTDOWN'))
		{
			//var_dump($obj);
			echo '! ' . get_class($obj) . ($details ? ' (' . $details . ')' : '') . "\n";
		}
	}

	register_shutdown_function('onShutDown');
	*/

	ob_start();

	// session cookie expires in 180 days
	session_set_cookie_params(180 * 60 * 60 * 24);

	include_once (include 'appdir.php') . '/application/Initialize.php';

	ClassLoader::import('application.LiveCart');

	$app = new LiveCart();

	if (isset($stat))
	{
		$app->setStatHandler($stat);
		$stat->logStep('Initialization');
	}

	// Custom initialization tasks
	$custom = ClassLoader::getRealPath('storage.configuration.CustomInitialize') . '.php';
	if (file_exists($custom))
	{
		include $custom;
	}

	if (version_compare('5.2', PHP_VERSION, '>'))
	{
		ClassLoader::import('library.json.json');
	}

	function runApp(LiveCart $app)
	{
		static $attempts = 0;

		// check if we're not getting into an endless loop
		if (++$attempts > 5)
		{
			try
			{
				$app->run();
			}
			catch (Exception $e)
			{
				dump_livecart_trace($e);
				die('error');
			}
		}

		try
		{
			if ($app->isDevMode())
			{
				try
				{
					$app->run();
				}
				catch (Exception $e)
				{
					if (!$e instanceof HTTPStatusException)
					{
						dump_livecart_trace($e);
					}
					else
					{
						throw $e;
					}
				}
			}
			else
			{
				$app->run();
			}
		}
		catch (HTTPStatusException $e)
		{
			if($e->getController() instanceof BackendController)
			{
				$route = 'backend.err/redirect/' . $e->getStatusCode();
			}
			else
			{
				$route = 'err/redirect/' . $e->getStatusCode();
			}

			$app->getRouter()->setRequestedRoute($route);
			runApp($app);
		}

		catch (ARNotFoundException $e)
		{
			$app->getRouter()->setRequestedRoute('err/redirect/404');
			runApp($app);
		}

		catch (ControllerNotFoundException $e)
		{
			$app->getRouter()->setRequestedRoute('err/redirect/404');
			runApp($app);
		}

		catch (ActionNotFoundException $e)
		{
			$app->getRouter()->setRequestedRoute('err/redirect/404');
			runApp($app);
		}

		catch (UnsupportedBrowserException $e)
		{
			header('Location: ' . $app->getRouter()->createUrl(array('controller' => 'err', 'action' =>'backendBrowser')));
			exit;
		}

		catch (SQLException $e)
		{
			$_REQUEST['exception'] = $e;
			$app->getRouter()->setRequestedRoute('err/database');
			runApp($app);
		}

		catch (Exception $e)
		{
			$route = 'err/redirect/500';
			$app->getRouter()->setRequestedRoute($route);
			runApp($app);
		}
	}

	runApp($app);

	if (!empty($_GET['stat']))
	{
		$stat->display();
	}

	function dump_livecart_trace(Exception $e)
	{
		echo "<br/><strong>" . get_class($e) . " ERROR:</strong> " . $e->getMessage()."\n\n";
		echo "<br /><strong>FILE TRACE:</strong><br />\n\n";
		echo ApplicationException::getFileTrace($e->getTrace());
		exit;
	}

	if (!isset($classLoaderCache) || ClassLoader::isCacheChanged())
	{
		$classLoaderCache = array('realPath' => ClassLoader::getRealPathCache(), 'mountPoint' => ClassLoader::getMountPointCache());
		file_put_contents($classLoaderCacheFile, '<?php return ' . var_export($classLoaderCache, true) . '; ?>');
	}

	session_write_close();

?>
