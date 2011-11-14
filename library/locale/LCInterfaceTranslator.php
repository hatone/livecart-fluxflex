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

include_once('LCiTranslator.php');

/**
 *
 * @package library.locale
 * @author Integry Systems
 */
class LCInterfaceTranslator implements LCiTranslator
{
	/**
	 * Locale_MakeText handler instance for the particular locale
	 */
	private $makeTextInstance = false;

	/**
	 * Translation manager object instance
	 */
	private $translationManager;

	/**
	 * Current locale code
	 */
	private $localeCode;

	/**
	 * Initializes the interface translator handler object and assigns translation manager object
	 * @param string $localeCode, LCInterfaceTranslationManager $manager
	 */
	public function __construct($localeCode, LCInterfaceTranslationManager $manager)
	{
		$this->localeCode = $localeCode;
		$this->translationManager = $manager;
	}

	/**
	 * Translates text to current locale.
	 * @param string $key
	 * @return string
	 */
	public function translate($key)
	{
		$def = $this->translationManager->getDefinition($key);

		return (FALSE !== $def) ? $def : $key;
	}

	/**
	 * Performs MakeText translation
	 * @param string $key
	 * @param array $params
	 * @return string
	 * @todo document its working principles and probably refactor as well
	 */
	public function makeText($key, $params)
	{
		$def = $this->translationManager->getDefinition($key)
				or $def = $key;

		$lh = $this->getLocaleMakeTextInstance();
		$list = array();
		$list[] = $this->translate($key);

		$list = is_array($params) ? array_merge($list, $params) : array_merge($list, array($params));

		return stripslashes(call_user_func_array(array($lh, "_"), $list));
	 }

	/**
	 * Creates MakeText handler for current locale
	 * @return LocaleMakeText
	 */
	private function getLocaleMakeTextInstance()
	{
	  	if (!$this->makeTextInstance)
		{
			require_once('maketext/LCMakeTextFactory.php');
			$inst = LCMakeTextFactory::create($this->localeCode);

			if ($inst)
			{
			  	$this->makeTextInstance = $inst;
			}
			else
			{
			  	return false;
			}
		}

		return $this->makeTextInstance;
	}

}

?>
