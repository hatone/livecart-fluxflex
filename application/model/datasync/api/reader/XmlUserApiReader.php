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

ClassLoader::import('application.model.datasync.api.reader.ApiReader');

/**
 * User model API XML format request parsing (reading/routing)
 *
 * @package application.model.datasync
 * @author Integry Systems <http://integry.com>
 */

class XmlUserApiReader extends ApiReader
{
	protected $xmlKeyToApiActionMapping = array
	(
		'list' => 'filter'
	);

	public static function getXMLPath()
	{
		return '/request/customer';
	}

	public function populate($updater, $profile)
	{
		parent::populate($updater, $profile, $this->xml, 
			self::getXMLPath().'/[[API_ACTION_NAME]]/[[API_FIELD_NAME]]', array('ID','email'));
	}

	public function getARSelectFilter()
	{
		return parent::getARSelectFilter('User');
	}

	public function getExtraFilteringMapping()
	{
		if(count($this->extraFilteringMapping) == 0)
		{
			$this->extraFilteringMapping = array(
				'id' => array(self::AR_FIELD_HANDLE=>f('User.ID'), self::AR_CONDITION=>'EqualsCond'),
				'name' => array(
					self::AR_FIELD_HANDLE => new ARExpressionHandle("CONCAT(User.firstName,' ',User.lastName)"),
					self::AR_CONDITION => 'LikeCond'),
				'created' => array(self::AR_FIELD_HANDLE => f('User.dateCreated'), self::AR_CONDITION => 'EqualsCond'),
				'enabled' => array(self::AR_FIELD_HANDLE => f('User.isEnabled'), self::AR_CONDITION => 'EqualsCond')
			);
		}
		return $this->extraFilteringMapping;
	}

	public function loadDataInRequest($request)
	{
		$apiActionName = $this->getApiActionName();
		$shortFormatActions = array('get','delete'); // like <customer><delete>[customer id]</delete></customer>
		if(in_array($apiActionName, $shortFormatActions))
		{
			$request = parent::loadDataInRequest($request, '//', $shortFormatActions);
			$request->set('ID',$request->get($apiActionName));
			$request->remove($apiActionName);
		} else {
			$request = parent::loadDataInRequest($request, self::getXMLPath().'//', $this->getApiFieldNames());
		}
		return $request;
	}

	public function sanitizeFilterField($name, &$value)
	{
		switch($name)
		{
			case 'enabled':
				$value = in_array(strtolower($value), array('y','t','yes','true','1')) ? true : false;
				break;
		}
		return $value;
	}
}
?>
