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
 * @package activerecord
 * @author Integry Systems
 */
class ARSerializedReference implements Serializable
{
	protected $instanceClassName;

	protected $instanceId;

	public function __construct(ActiveRecord $instance)
	{
		$this->instanceClassName = get_class($instance);
		$this->instanceId = $instance->getID();
	}

	public function restoreInstance()
	{
		$instance = ActiveRecord::getInstanceById($this->instanceClassName, $this->instanceId);
		return $instance;
	}

	public function getClassName()
	{
		return $this->instanceClassName;
	}

	public function serialize()
	{
		$serialized = array();
		$serialized['class'] = $this->instanceClassName;
		$serialized['ID'] = $this->instanceId;
		return serialize($serialized);
	}

	public function unserialize($serialized)
	{
		$data = unserialize($serialized);
		$this->instanceClassName = $data['class'];
		$this->instanceId = $data['ID'];
	}
}

?>
