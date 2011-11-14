<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
include '../application/Initialize.php';
ClassLoader::import('application.LiveCart');
$application = new LiveCart();

define('BUFFER', 50);

ClassLoader::import('application.model.product.Product');

$languages = $application->getLanguageArray();
$default = $application->getDefaultLanguageCode();

if (!$languages)
{
	die('No additional languages enabled');
}

$count = ActiveRecordModel::getRecordCount('Product', new ARSelectFilter());
$parts = ceil($count / BUFFER);
$fields = array('name', 'shortDescription', 'longDescription');

ActiveRecordModel::beginTransaction();

for ($k = 0; $k < $parts; $k++)
{
	$filter = new ARSelectFilter();
	$filter->setLimit(BUFFER, BUFFER * $k);
	$filter->setOrder(new ARFieldHandle('Product', 'ID'));

	$products = ActiveRecordModel::getRecordSet('Product', $filter);
	foreach ($products as $product)
	{
		foreach ($fields as $field)
		{
			if (!$product->getValueByLang($field, $default))
			{
				foreach ($languages as $lang)
				{
					if ($value = $product->getValueByLang($field, $lang))
					{
						$product->setValueByLang($field, $default, $value);
						break;
					}
				}
			}
		}

		$product->save();
	}
}

ActiveRecordModel::commit();

?>