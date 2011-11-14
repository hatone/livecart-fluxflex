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

include_once dirname(__file__) . '/MassActionProcessor.php';

/**
 * @package application.helper.massAction
 * @author Integry Systems
 */
class OrderMassActionProcessor extends MassActionProcessor
{
	protected function processRecord(CustomerOrder $order)
	{
		$order->processMass_history = new OrderHistory($order, SessionUser::getUser());

		switch($this->getAction())
		{
			case 'setNew':
				$status = CustomerOrder::STATUS_NEW;
				break;
			case 'setProcessing':
				$status = CustomerOrder::STATUS_PROCESSING;
				break;
			case 'setAwaitingShipment':
				$status = CustomerOrder::STATUS_AWAITING;
				break;
			case 'setShipped':
				$status = CustomerOrder::STATUS_SHIPPED;
				break;
			case 'setReturned':
				$status = CustomerOrder::STATUS_RETURNED;
				break;
			case 'setUnfinalized':
				$order->isFinalized->set(0);
				break;
			case 'setCancel':
				$order->cancel();
				break;
			case 'setFinalized':
				if (!$order->isFinalized->get() && $order->user->get())
				{
					$order->finalize();
				}
				break;
		}

		if (isset($status) && ($status != $order->status->get()))
		{
			$order->setStatus($status);
			$this->params['controller']->sendStatusNotifyEmail($order);
		}
	}

	protected function saveRecord(CustomerOrder $order)
	{
		parent::saveRecord($order);
		if (!in_array($this->getAction(), array('setFinalized', 'delete')))
		{
			$order->processMass_history->saveLog();
			unset($order->processMass_history);
		}
	}
}

?>
