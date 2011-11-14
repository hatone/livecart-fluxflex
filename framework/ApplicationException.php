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
 * General exception which might be raised within an application context
 *
 * @package framework
 * @author Integry Systems
 */
class ApplicationException extends Exception
{

	public static function getFileTrace($trace)
	{
		$showedFiles = array();
		$i = 0;
		$traceString = '';

		$ajax = false; //isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? true : false;


		// Get new line
		$newLine = $ajax ? "\n" : "<br />\n";

		foreach($trace as $call)
		{
			if(isset($call['file']) && isset($call['line']) && !isset($showedFiles[$call['file']][$call['line']]))
			{
				$showedFiles[$call['file']][$call['line']] = true;

				// Get file name and line
				if($ajax)
				{
					$position = ($i++).": {$call['file']}:{$call['line']}";
				}
				else
				{
					$position = "<strong>".($i++)."</strong>: \"{$call['file']}\":{$call['line']}";
				}

				// Get function name
				if(isset($call['class']) && isset($call['type']) && isset($call['function']))
				{
					$functionName = "{$call['class']}{$call['type']}{$call['function']}";
				}
				else
				{
					$functionName = $call['function'];
				}

				// Get function arguments
				$arguments = '';
				$j = 1;
				if (isset($call['args']))
				{
					foreach($call['args'] as $argv)
					{
						switch(gettype($argv))
						{
							case 'string':
								$arguments .= "\"$argv\"";
							break;
							case 'boolean':
								 $arguments .= ($argv ? 'true' : 'false');
							break;
							case 'integer':
							case 'double':
								 $arguments .= $argv;
							break;
							case 'object':
								 $arguments .= "(object)" . get_class($argv);
							break;
							case 'array':
								 $arguments .= "Array";
							break;
							default:
								$arguments .= $argv;
							break;
						}

						if($j < count($call['args'])) $arguments .= ", "; $j++;
					}
				}


				// format the output line
				$traceString .= "$newLine$position - $functionName($arguments)";
			}
		}

		return $traceString;
	}
}

?>
