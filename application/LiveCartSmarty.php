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

require_once ClassLoader::getRealPath('library.smarty.libs.') . 'Smarty.class.php';

/**
 *  Extends Smarty with LiveCart-specific logic
 *
 *  @package application
 *  @author Integry Systems
 */
class LiveCartSmarty extends Smarty
{
	private $application;

	private $paths = array();

	public function __construct(LiveCart $application)
	{
		$this->application = $application;
		$this->register_modifier('config', array($this, 'config'));
		$this->register_modifier('branding', array($this, 'branding'));
		$this->load_filter('pre', 'config');

		parent::__construct();
	}

	/**
	 * Get LiveCart application instance
	 *
	 * @return LiveCart
	 */
	public function getApplication()
	{
		return $this->application;
	}

	public function set($var, $value)
	{
		$this->assign($var, $value);
	}

	public function get($var)
	{
		return $this->get_template_vars($var);
	}

	/**
	 *  Retrieve software configuration values from Smarty templates
	 *
	 *  <code>
	 *	  {'STORE_NAME'|config}
	 *  </code>
	 */
	public function config($key)
	{
		$config = self::getApplication()->getConfig();
		if ($config->isValueSet($key))
		{
			return $config->get($key);
		}
	}

	/**
	 *  Replace "LiveCart" with alternative name if rebranding options are used
	 */
	public function branding($string)
	{
		$softName = self::getApplication()->getConfig()->get('SOFT_NAME');
		return 'LiveCart' != $softName ? str_replace('LiveCart', $softName, $string) : $string;
	}

	public function processPlugins($output, $path)
	{
		$path = substr($path, 0, -4);
		$path = str_replace('\\', '/', $path);

		$path = $this->translatePath($path);
		$path = preg_replace('/^\/*/', '', $path);
		$path = preg_replace('/\/{2,}/', '/', $path);

		if (substr($path, 0, 6) == 'theme/')
		{
			$themePath = $path;
			preg_match('/^theme\/.*\/(.*)$/U', $path, $res);
			$path = preg_replace('/^\/*/', '', array_pop($res));
		}

		$plugins = $this->getPlugins($path);
		if (isset($themePath))
		{
			$plugins = array_merge($plugins, $this->getPlugins($themePath));
		}

		foreach ($plugins as $plugin)
		{
			$output = $plugin->process($output);
		}

		return $output;
	}

	public function _smarty_include($params)
	{
		// strip custom:
		$path = substr($params['smarty_include_tpl_file'], 7);

		ob_start();
		parent::_smarty_include($params);
		$output = ob_get_contents();
		ob_end_clean();

		echo $this->application->getRenderer()->applyLayoutModifications($path, $output);
	}

	public function _compile_source($resource_name, &$source_content, &$compiled_content, $cache_include_path=null)
	{
		$source_content = $this->processPlugins($source_content, $resource_name);
		return parent::_compile_source($resource_name, $source_content, $compiled_content, $cache_include_path);
	}

   /**
     * Get the compile path for this resource
     *
     * @param string $resource_name
     * @return string results of {@link _get_auto_filename()}
     */
    public function _get_compile_path($resource_name)
    {
        if (substr($resource_name, 0, 7) == 'custom:')
        {
        	if (!function_exists('smarty_custom_get_path'))
        	{
        		include ClassLoader::getRealPath('application.helper.smarty.') . 'resource.custom.php';
			}

        	$resource_name = smarty_custom_get_path(substr($resource_name, 7), $this);
		}

        return $this->_get_auto_filename($this->compile_dir, $resource_name,
                                         $this->_compile_id) . '.php';
    }

    public function evalTpl($code)
    {
		$this->_compile_source('evaluated template', $code, $compiled);

		ob_start();
		$this->_eval('?>' . $compiled);
		$_contents = ob_get_contents();
		ob_end_clean();

		return $_contents;
	}

	public function disableTemplateLocator()
	{
		if (!empty($this->_plugins['prefilter']['templateLocator']))
		{
			$this->isTemplateLocator = true;
			$this->unregister_prefilter('templateLocator');
			unset($this->_plugins['prefilter']['templateLocator']);
		}
	}

	public function enableTemplateLocator()
	{
		if (!empty($this->templateLocator))
		{
			$this->_plugins['prefilter']['templateLocator'] = $this->templateLocator;
			unset($this->templateLocator);
		}
	}

	private function translatePath($path)
	{
		if (substr($path, 0, 7) == 'custom:')
		{
			$path = substr($path, 7);
		}

		if (substr($path, 0, 1) == '@')
		{
			$path = substr($path, 1);
		}

		if ($relative = LiveCartRenderer::getRelativeTemplatePath($path))
		{
			$path = $relative;
		}

		return $path;
	}

	private function getPlugins($path)
	{
		if (!class_exists('ViewPlugin', false))
		{
			ClassLoader::import('application.ViewPlugin');
		}

		if ('/' == $path[0])
		{
			$path = substr($path, 1);
		}

		$plugins = array();
		foreach ($this->getApplication()->getPlugins('view/' . $path) as $plugin)
		{
			include_once $plugin['path'];
			$plugins[] = new $plugin['class']($this, $this->application);
		}

		return $plugins;
	}
}

?>
