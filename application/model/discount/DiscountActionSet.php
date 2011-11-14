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

ClassLoader::import('library.activerecord.ARSet');
ClassLoader::import('application.model.discount.DiscountAction');

/**
 *
 * @package application.model.discount
 * @author Integry Systems <http://integry.com>
 */
class DiscountActionSet extends ARSet
{
	public function getActionsByType($type)
	{
		$result = new DiscountActionSet();
		foreach ($this as $rec)
		{
			if ($rec->actionType->get() == $type)
			{
				$result->add($rec);
			}
		}

		return $result;
	}
}

?>
