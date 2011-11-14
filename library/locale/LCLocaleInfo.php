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
 *
 * @package library.locale
 * @author Integry Systems
 */
class LCLocaleInfo
{
	/**
	 * Current locale code (en, lt, ru, ...)
	 */
	private $localeCode;

	/**
	 * Date format array
	 */
	private $dateFormats;

	/**
	 * Time format array
	 */
	private $timeFormats;

	/**
	 * Country data handler instance
	 */
	private $countryInstance;

	/**
	 * Language data handler instance
	 */
	private $languageInstance;

	/**
	 * Currency data handler instance
	 */
	private $currencyInstance;

	/**
	 * Language original names (ex: lietuviu, Deutsch, eesti)
	 */
	private $originalNames = false;

	public function __construct($localeCode)
	{
		$this->localeCode = $localeCode;
	}

	/**
	 * Gets country name by code.
	 * $param string $code
	 * @return string
	 */
	public function getCountryName($code)
	{
	  	return $this->getCountryInstance()->getName($code);
	}

	/**
	 * Gets array of country names.
	 * @return array
	 */
	public function getAllCountries($first = null)
	{
		$countries = $this->getCountryInstance()->getAllCodes();

		foreach (array('SU', 'DD', 'NT') as $unexisting)
		{
			unset($countries[$unexisting]);
		}

	  	asort($countries);

	  	if ($first)
	  	{
	  		$firstCountry = array_intersect_key(array($first => $countries[$first]), $countries);
	  		$rest = array_diff_key($countries, array($first => ''));
	  		$countries = array_merge($firstCountry, $rest);
		}

		return $countries;
	}

	/**
	 * Get country codes arranged in group by their
	 *
	 * @return unknown
	 */
	public function getCountryGroups()
	{
		return array(
			'Africa' => array(
				'DZ','AO','BJ','BW','BF','BI','CM','CV','CF','TD','KM','CG','CD','DJ',
				'EG','GQ','ER','ET','GA','GM','GH','GN','GW','CI','KE','LS','LR','LY',
				'MG','MW','ML','MR','MU','MA','MZ','NA','NE','NG','RW','ST','SN','SC',
				'SL','SO','ZA','SD','SZ','TZ','TG','TN','UG','ZM','ZW'
			),
			'Asia' => array(
				'AF','BH','BD','BT','BN','MM','KH','CN','TL','IN','ID','IR','IQ','IL',
				'JP','JO','KZ','KP','KR','KW','KG','LA','LB','MY','MV','MN','NP','OM',
				'PK','PH','QA','','RU','SA','SG','LK','SY','TJ','TH','TR','TM','AE',
				'UZ','VN','YE'
			),
			'Europe' => array(
				'AL','AD','AM','AT','AZ','BY','BE','BA','BG','HR','CY','CZ','DK','EE',
				'FI','FR','GE','DE','GR','HU','IS','IE','IT','LV','LI','LT','LU','MK',
				'MT','MD','MC','NL','NO','PL','PT','RO','SM','SP','SK','SI','ES','SE',
				'CH','UA','GB','VA'
			),
			'North America' => array(
				'AG','BS','BB','BZ','CA','CR','CU','DM','SV','GD','GT','HT','HN','JM',
				'MX','NI','PA','KN','LC','VC','TT','US'
			),
			'South America' => array(
				'AR','BO','BR','CL','CO','EC','GY','PY','PE','SR','UY','VE'
			),
			'Oceania' => array(
				'AU','FJ','KI','MH','FM','NR','NZ','PW','PG','WS','SB','TO','TV','VU'
			),
			'European Union' => array(
				'BE','FR','DE','IT','LU','NL','DK','IE','GB','GR','PT','ES','AT','FI',
				'SE','CY','CZ','EE','LV','LT','HU','MT','PL','SK','SI','BG','RO'
			),
		);
	}

	/**
	 * Gets array of language names.
	 * @return array
	 */
	public function getAllLanguages()
	{
	  	return $this->getLanguageInstance()->getAllCodes();
	}

	/**
	 * Gets language name by code
	 * $param string $code
	 * @return string
	 */
	public function getLanguageName($code)
	{
	  	return $this->getLanguageInstance()->getName($code);
	}

	/**
	 * Gets language name in its language by code
	 * ex: "lt" will always return "Lietuviu" regardless of the current locale
	 * $param string $code
	 * @return string
	 */
	public function getOriginalLanguageName($code)
	{
	  	if (!$this->originalNames)
	  	{
			include dirname(__file__).'/I18Nv2/Language/original.php';
			$this->originalNames = $names;
		}

		if (isset($this->originalNames[$code]))
		{
		  	return $this->originalNames[$code];
		}
		else
		{
		  	return $this->getLanguageName($code);
		}
	}

	/**
	 * Gets array of currency names.
	 * @return array
	 */
	public function getAllCurrencies()
	{
		include dirname(__file__).'/I18Nv2/Currency/activeCurrencies.php';
	  	$currencyCodes = $this->getCurrencyInstance()->getAllCodes();
	  	return array_intersect_key($currencyCodes, $activeCurrencies);
	}

	/**
	 * Gets currency name by code.
	 * @param string $code
	 * $return string
	 */
	public function getCurrencyName($code)
	{
		return $this->getCurrencyInstance()->getName($code);
	}

	/**
	 * Returns time format for current locale
	 * @param string $format (ex: default, short, medium, long, full)
	 * $return string Time format (ex: %H:%M:%S)
	 */
	public function getTimeFormat($format = 'default')
	{
		$this->loadLocaleData();
		if (isset($this->timeFormats[$format]))
		{
			return $this->timeFormats[$format];
		}
		else
		{
			return $this->timeFormats['default'];
		}
	}

	/**
	 * Returns date format for current locale
	 * @param string $format (ex: default, short, medium, long, full)
	 * $return string Date format (ex: %d-%b-%Y)
	 */
	public function getDateFormat($format = 'default')
	{
		$this->loadLocaleData();
		if (isset($this->dateFormats[$format]))
		{
			return $this->dateFormats[$format];
		}
		else
		{
			return $this->dateFormats['default'];
		}
	}

	/**
	 * Loads locale data file (time, date formats)
	 */
	private function loadLocaleData()
	{
		if (!$this->dateFormats)
		{

			if (!defined('I18Nv2_DATETIME_SHORT'))
			{
				/**#@+ Constants **/
				define('I18Nv2_NUMBER',					 'number');
				define('I18Nv2_CURRENCY',				   'currency');
				define('I18Nv2_DATE',					   'date');
				define('I18Nv2_TIME',					   'time');
				define('I18Nv2_DATETIME',				   'datetime');

				define('I18Nv2_NUMBER_FLOAT' ,			  'float');
				define('I18Nv2_NUMBER_INTEGER' ,			'integer');

				define('I18Nv2_CURRENCY_LOCAL',			 'local');
				define('I18Nv2_CURRENCY_INTERNATIONAL',	 'international');

				define('I18Nv2_DATETIME_SHORT',			 'short');
				define('I18Nv2_DATETIME_DEFAULT',		   'default');
				define('I18Nv2_DATETIME_MEDIUM',			'medium');
				define('I18Nv2_DATETIME_LONG',			  'long');
				define('I18Nv2_DATETIME_FULL',			  'full');
				/**#@-*/
			}

			include dirname(__file__) . '/I18Nv2/Locale/en.php';
			$file = dirname(__file__) . '/I18Nv2/Locale/' . $this->localeCode . '.php';
			if (file_exists($file))
			{
				include $file;
			}
		}
	}

	/**
	 * Returns language data handler instance
	 * $return I18Nv2_Language
	 */
	private function getLanguageInstance() {

	  	if (!$this->languageInstance)
		{
	  	 	include_once dirname(__file__) . '/I18Nv2/Language.php';
			$this->languageInstance = new I18Nv2_Language($this->localeCode);
		}

		return $this->languageInstance;
	}

	/**
	 * Returns country data handler instance
	 * $return I18Nv2_Country
	 */
	private function getCountryInstance()
	{
	  	if (!$this->countryInstance)
		{
	  	 	include_once dirname(__file__) . '/I18Nv2/Country.php';
			$this->countryInstance = new I18Nv2_Country($this->localeCode);
		}

		return $this->countryInstance;
	}

	/**
	 * Returns currency data handler instance
	 * $return I18Nv2_Country
	 */
	private function getCurrencyInstance()
	{
	  	if (!$this->currencyInstance)
		{
	  	 	include_once dirname(__file__) . '/I18Nv2/Currency.php';
			$this->currencyInstance = new I18Nv2_Currency($this->localeCode);
		}

		return $this->currencyInstance;
	}
}

?>
