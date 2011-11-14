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

/**
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductVariationTypeSet extends ARSet
{
	public function getVariations()
	{
		$f = new ARSelectFilter(new INCOnd(new ARFieldHandle('ProductVariation', 'typeID'), $this->getRecordIDs()));
		$f->setOrder(new ARFieldHandle('ProductVariation', 'position'));

		return ActiveRecordModel::getRecordSet('ProductVariation', $f);
	}
}

?>
