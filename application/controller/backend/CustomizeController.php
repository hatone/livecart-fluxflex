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
ClassLoader::import("application.model.system.CssFile");

/**
 * Handles dynamic interface customizations
 *
 * @package application.controller.backend
 * @author Integry Systems
 *
 * @role customize
 */
class CustomizeController extends StoreManagementController
{
	public function index()
	{
		$response = new ActionResponse();
		$response->set('isCustomizationModeEnabled', $this->application->isCustomizationMode());
		$response->set('isTranslationModeEnabled', $this->application->isTranslationMode());
		return $response;
	}

	public function translationMode()
	{
	  	if ($this->application->isTranslationMode())
	  	{
			$this->session->unsetValue('translationMode');
		}
		else
		{
			$this->session->set('translationMode', true);
		}

		return new ActionRedirectResponse('backend.customize', 'index');
	}

	public function mode()
	{
	  	if (($this->application->isCustomizationMode() && !$this->request->isValueSet('mode')) || ('exit' == $this->request->get('mode')))
	  	{
			$this->session->unsetValue('customizationMode');
		}
		else
		{
			$this->session->set('customizationMode', true);
			$this->session->set('customizationModeType', $this->request->get('mode', 'template'));
		}

		if (!$this->request->isValueSet('return'))
		{
			return new ActionRedirectResponse('backend.customize', 'index');
		}
		else
		{
			return new RedirectResponse($this->router->createUrlFromRoute($this->request->get('return')));
		}
	}

	public function saveCss()
	{
		$params = json_decode($this->request->get('result'), true);

		$theme = $params['theme'];

		if (!$theme)
		{
			$theme = 'barebone';
		}

		// save custom CSS
		$css = new CssFile('upload/css/' . $theme . '.css');
		$css->setSource($params['css']);
		$css->save();

		// deleted rules
		foreach ($params['deletedRules'] as $file => $selectors)
		{
			$css = CssFile::getInstanceFromUrl($file, $theme);
			foreach ($selectors as $selector)
			{
				$css->deleteSelector($selector);
			}

			$css->save();
		}

		// deleted properties
		foreach ($params['deletedProperties'] as $file => $selectors)
		{
			$css = CssFile::getInstanceFromUrl($file, $theme);
			foreach ($selectors as $selector => $properties)
			{
				foreach ($properties as $property => $value)
				{
					$css->deleteProperty($selector, $property);
				}
			}

			$css->save();
		}
	}


}

?>
