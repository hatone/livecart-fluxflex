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

ClassLoader::import("application.model.report.Report");

/**
 * Generate sales reports
 *
 * @package application.model.report
 * @author	Integry Systems
 */
class SalesReport extends Report
{
	protected function getMainTable()
	{
		return 'CustomerOrder';
	}

	protected function getDateHandle()
	{
		return new ARFieldHandle('CustomerOrder', 'dateCompleted');
	}

	public function getOrderCounts()
	{
		$this->getData('COUNT(*)');
	}

	public function getOrderTotals()
	{
		$this->getData('ROUND(SUM(totalAmount * ' . $this->getCurrencyMultiplier() . '), 2)');
	}

	public function getOrderedItemCounts()
	{
		$q = $this->getQuery('COUNT(OrderedItem.ID)');
		$q->joinTable('OrderedItem', 'CustomerOrder', 'customerOrderID', 'ID');
		$this->getReportData($q);
	}

	public function getAvgOrderTotals()
	{
		$this->getData('ROUND(SUM(totalAmount * ' . $this->getCurrencyMultiplier() . ') / COUNT(dateCompleted), 2)');
	}

	public function getAvgItemCounts()
	{
		$this->getData('ROUND(SUM((SELECT COUNT(OrderedItem.ID) * OrderedItem.count FROM OrderedItem WHERE OrderedItem.customerOrderID=CustomerOrder.ID)) / COUNT(dateCompleted), 2)');
	}

	public function getPaymentMethodCounts()
	{
		$this->setChartType(self::PIE);
		$q = $this->getQuery('COUNT(*)');
		$q->joinTable('Transaction', 'CustomerOrder', 'orderID', 'ID');

		$f = $q->getFilter();
		$q->addField('method', null, 'entry');
		$f->setGrouping(new ARFieldHandle('Transaction', 'method'));
		$f->mergeCondition(new EqualsCond(new ARFieldHandle('CustomerOrder', 'isPaid'), true));
		$f->mergeCondition(new INCond(new ARFieldHandle('Transaction', 'type'), array(Transaction::TYPE_SALE, Transaction::TYPE_CAPTURE)));

		$this->getReportData($q);

		foreach ($this->values['x'] as $value)
		{
			if (!$value->originalName)
			{
				$value->originalName = '_offline_payments';
			}

			$value->label = $this->application->translate($value->originalName) . ' (' . $value->value . ')';
		}
	}

	public function getCurrencyCounts()
	{
		$this->setChartType(self::PIE);
		$q = $this->getQuery('COUNT(*)');

		$f = $q->getFilter();
		$q->addField('currencyID', null, 'entry');
		$f->setGrouping(new ARFieldHandle('CustomerOrder', 'currencyID'));
		$f->mergeCondition(new EqualsCond(new ARFieldHandle('CustomerOrder', 'isPaid'), true));

		$this->getReportData($q);
	}

	public function getStatuses()
	{
		$this->setChartType(self::PIE);
		$q = $this->getQuery('COUNT(*)');

		$f = $q->getFilter();
		$q->addField('IF (isCancelled = 1, -2, IF (isPaid = 0, -1, IF (status IS NULL, 0, status)))', null, 'entry');
		$f->setGrouping(new ARExpressionHandle('entry'));

		$this->getReportData($q);

		foreach ($this->values['x'] as $value)
		{
			$value->label = $this->application->translate(CustomerOrder::getStatusName($value->originalName));
		}
	}

	public function getCancelledRatio()
	{
		$this->getData('ROUND((SUM(isCancelled)/COUNT(*)) * 100, 2)');
	}

	public function getUnpaidRatio()
	{
		$this->getData('ROUND(((COUNT(*) - SUM(isPaid))/COUNT(*)) * 100, 2)');
	}

	protected function getQuery($countSql = null)
	{
		$q = parent::getQuery($countSql);
		$q->getFilter()->mergeCondition(new EqualsCond(new ARFieldHandle('CustomerOrder', 'isFinalized'), true));

		return $q;
	}
}

?>
