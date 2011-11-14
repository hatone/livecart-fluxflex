<?php

include '../application/Initialize.php';
ClassLoader::import('application.LiveCart');
new LiveCart();

ClassLoader::import('application.model.product.ProductImage');

$dir = ClassLoader::getRealPath('public.upload.productimage');
if (!file_exists($dir))
{
	return false;
}

$ids = array();
foreach (ActiveRecord::getDataBySQL('SELECT ID FROM ProductImage') as $id)
{
	$ids[$id['ID']] = true;
}

chdir($dir);

$deleted = array();
$deletedCnt = 0;
foreach (glob('*') as $file)
{
	list($productId, $id, $foo) = explode('-', $file, 3);
	if (!isset($ids[$id]))
	{
		$deleted[$id] = true;
		$deletedCnt++;
		unlink($file);
	}
}

echo 'Image cleanup completed. Deleted ' . $deletedCnt . ' image files for ' . count($deleted) . ' product images';

?>