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
 * Sequentially read and iterate over records from database
 *
 * This iterator can be used as data array in foreach and it allows to
 * read a very high number of records, as only a small chunk of them is
 * being kept in memory at any time.
 *
 * @author Integry Systems
 * @package application.model.
 */
class ARFeed implements Iterator, Countable
{
	protected $position = 0;
	protected $size = 0;
	protected $data = array();
	protected $from = -1;
	protected $to = -1;

	protected $filter, $table, $referencedRecords;

	protected $flush = false;

	const CHUNK_SIZE = 100;

	public function __construct(ARSelectFilter $filter, $table, $referencedRecords = null)
	{
		$this->filter = $filter;
		$this->table = $table;
		$this->referencedRecords = $referencedRecords;

		$this->size = ActiveRecord::getRecordCount($this->table, $filter, $referencedRecords);
	}

	public function current()
	{
		return $this->fetch($this->position);
	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		++$this->position;
	}

	public function rewind()
	{
		$this->position = 0;
	}

	public function valid()
	{
		return $this->position < $this->size;
	}

	public function count()
	{
		return $this->size;
	}

	public function setFlush($flush = true)
	{
		$this->flush = $flush;
	}

	protected function &fetch($pos)
	{
		if (!(($pos >= $this->from) && ($pos < $this->to)))
		{
			$this->from = $pos;
			$this->to = $pos + $this->getChunkSize();

			ActiveRecord::clearPool();
			$this->filter->setLimit($this->getChunkSize(), $this->from);
			$this->loadData();

			$this->postProcessData();
		}

		if ($this->flush)
		{
			//echo '|' . round(memory_get_usage() / (1024*1024), 1) . "\n";
			flush();
			ob_flush();
		}

		$offset = $pos - $this->from;

		return $this->data[$offset];
	}

	protected function loadData()
	{
		ActiveRecord::clearArrayData();
		$this->data = ActiveRecord::getRecordSetArray($this->table, $this->filter, $this->referencedRecords);
	}

	protected function getChunkSize()
	{
		return self::CHUNK_SIZE;
	}

	protected function postProcessData()
	{

	}
}

?>
