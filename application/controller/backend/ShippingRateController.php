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
ClassLoader::import("application.model.delivery.ShippingService");
ClassLoader::import("application.model.delivery.ShippingRate");

/**
 * Application settings management
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role delivery
 */
class ShippingRateController extends StoreManagementController
{
	/**
	 * @role update
	 */
	public function delete()
	{
		if($id = (int)$this->request->get('id'))
		{
			ShippingRate::getInstanceByID($id)->delete();
		}

		return new JSONResponse(false, 'success');
	}
}
?>
