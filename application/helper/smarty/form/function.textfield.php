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
 * Renders text field
 *
 * If you wish to use autocomplete on a text field an additional parameter needs to be passed:
 *
 * <code>
 *	  autocomplete="controller=somecontroller field=fieldname"
 * </code>
 *
 * The controller needs to implement an autoComplete method, which must return the AutoCompleteResponse
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 *
 * @package application.helper.smarty.form
 * @author Integry Systems
 */
function smarty_function_textfield($params, LiveCartSmarty $smarty)
{
	$formParams = $smarty->_tag_stack[0][1];
	$formHandler = $formParams['handle'];
	$fieldName = $params['name'];
	if (!($formHandler instanceof Form))
	{
		throw new HelperException('Element must be placed in {form} block');
	}

	if (!isset($params['type']))
	{
		$params['type'] = 'text';
	}

	// Check permissions
	if($formParams['readonly'])
	{
		$params['readonly'] = 'readonly';
	}

	$value = array_pop(array_filter(array($params['value'], $params['default'], $formHandler->get($fieldName))));

	unset($params['value'], $params['default']);

	$content = '<input';
	foreach ($params as $name => $param) {
		$content .= ' ' . $name . '="' . $param . '"';
	}

	$content .= ' value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
	$content .= '/>';
	if (isset($params['autocomplete']) && $params['autocomplete'] != 'off')
	{
	  	$acparams = array();
		foreach (explode(' ', $params['autocomplete']) as $param)
	  	{
			list($p, $v) = explode('=', $param, 2);
			$acparams[$p] = $v;
		}

		$url = $smarty->getApplication()->getRouter()->createURL(array('controller' => $acparams['controller'],
													  'action' => 'autoComplete',
													  'query' => 'field=' . $acparams['field']), true);

		$content .= '<span id="autocomplete_indicator_' . $params['id'] . '" class="progressIndicator" style="display: none;"></span>';
		$content .= '<div id="autocomplete_' . $params['id'] . '" class="autocomplete"></div>';
		$content .= '<script type="text/javascript">
						new Ajax.Autocompleter("' . $params['id'] . '", "autocomplete_' . $params['id'] . '", "' . $url . '", {frequency: 0.5, paramName: "' . $acparams['field'] . '", indicator: "autocomplete_indicator_' . $params['id'] . '"});
					</script>';
	}

	return $content;
}

?>
