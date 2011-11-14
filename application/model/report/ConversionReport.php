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
class ConversionReport extends Report
{
	protected function getMainTable()
	{
		return 'CustomerOrder';
	}

	protected function getDateHandle()
	{
		return new ARFieldHandle('CustomerOrder', 'dateCreated');
	}

	public function getConversionRatio()
	{
		$this->getData('ROUND((SUM(isFinalized) / COUNT(*)) * 100, 2)');
	}

	public function getCheckoutSteps()
	{
		$this->setChartType(self::PIE);
		$q = $this->getQuery('COUNT(*)');

		$f = $q->getFilter();
		$q->addField('checkoutStep', null, 'entry');
		$f->setGrouping(new ARExpressionHandle('entry'));
		$f->mergeCondition(new EqualsCond(new ARFieldHandle('CustomerOrder', 'isFinalized'), 0));

		$this->getReportData($q);

		foreach ($this->values['x'] as $value)
		{
			$value->label = $this->application->translate('_progress_' . $value->originalName) . ' (' . $value->value . ')';
		}
	}

	public function getCartCounts()
	{
		$this->getData('COUNT(*)');
	}
}

?>
