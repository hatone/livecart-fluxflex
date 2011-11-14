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

ClassLoader::import("framework.response.Response");

/**
 * JSON response
 *
 * @package framework.response
 * @author	Integry Systems
 */
class JSONResponse extends Response
{
	private $content;

	private $data;

	public function __construct($data, $status = false, $message = false)
	{
		$this->setHeader('Cache-Control', 'no-cache, must-revalidate');
		$this->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$this->setHeader('Content-type', 'text/javascript;charset=utf-8');

		if($message)
		{
			$data['message'] = $message;
		}

		if($status)
		{
			$data['status'] = strtolower($status);
		}

		$this->data = $data;
	}

	public function flush($string)
	{
		if (!headers_sent())
		{
			$this->sendHeaders();
			ob_flush();
			ob_end_clean();
			flush();
		}

		if (ob_get_length())
		{
			ob_end_flush();
		}

		echo $string;
		flush();
	}

	public function flushChunk($data)
	{
		$this->flush(base64_encode(json_encode($data)) . '|');
	}

	public function getValue()
	{
		return $this->data;
	}

	public function getData()
	{
		if (!$this->content)
		{
			$this->content = @json_encode($this->data);
		}

		return $this->content;
	}
}

?>
