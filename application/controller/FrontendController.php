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

ClassLoader::import('application.controller.BaseController');
ClassLoader::import('application.model.Currency');
ClassLoader::import('application.model.category.Category');
ClassLoader::import('application.model.product.ProductPrice');

/**
 * Base class for all front-end related controllers
 *
 * @author Integry Systems
 * @package application.controller
 */
abstract class FrontendController extends BaseController
{
	protected $breadCrumb = array();

	/**
	 *	Session order instance
	 */
	protected $order;

	public function __construct(LiveCart $application)
	{
		parent::__construct($application);

		$this->request->sanitizeAllData();

		unset($this->order);

		// variables to append automatically to all URLs
		foreach (array('currency', 'sort', 'layout') as $key)
		{
			if ($this->request->isValueSet($key))
			{
				$this->router->addAutoAppendQueryVariable($key, $this->request->get($key));
			}
		}

		// disallow creating new EAV select field values from frontend
		if (is_array($this->request->get('other')))
		{
			$this->request->remove('other');
		}

		if ($this->application->isCustomizationMode())
		{
			$this->loadLanguageFile('Customize');
		}
	}

	public function init()
	{
		$initRes = parent::init();

		$this->setLayout('frontend');
		$this->addBlock('FOOTER_TOOLBAR', 'toolbar', 'block/toolbar');
		$this->addBlock('CATEGORY_BOX', 'boxCategory', 'block/box/category');
		$this->addBlock('ROOT_CATEGORIES', 'boxRootCategory', 'block/box/rootCategory');
		$this->addBlock('DYNAMIC_CATEGORIES', 'dynamicCategoryMenu', 'block/box/dynamicCategory');
		$this->addBlock('SALE_ITEMS', 'saleItems', 'block/box/saleItems');
		$this->addBlock('NEWEST_PRODUCTS', 'newestProducts', 'block/box/newestProducts');
		$this->addBlock('BESTSELLING_PRODUCTS', 'bestsellingProducts', 'block/box/bestsellingProducts');
		$this->addBlock('BREADCRUMB', 'boxBreadCrumb', 'block/box/breadcrumb');
		$this->addBlock('BREADCRUMB_TITLE', 'boxBreadCrumbTitle', 'block/box/breadcrumbTitle');
		$this->addBlock('LANGUAGE', 'boxLanguageSelect', 'block/box/language');
		$this->addBlock('CURRENCY', 'boxSwitchCurrency', 'block/box/currency');
		$this->addBlock('CURRENCY_MENU', 'boxSwitchCurrencyMenu', 'block/box/currencyMenu');
		$this->addBlock('CART', 'boxShoppingCart', 'block/box/shoppingCart');
		$this->addBlock('SEARCH', 'boxSearch', 'block/box/search');
		$this->addBlock('INFORMATION', 'boxInformationMenu', 'block/box/informationMenu');
		$this->addBlock('NEWSLETTER', 'boxNewsletterSubscribe', 'block/box/newsletterSubscribe');
		$this->addBlock('TRACKING', 'tracking', 'block/tracking');
		$this->addBlock('NEWS', 'latestNews', 'block/box/latestNews');
		$this->addBlock('QUICKNAV', 'blockQuickNav', 'block/box/quickNav');
		$this->addBlock('COMPARE', array('compare', 'compareMenu'));
		$this->addBlock('MINI_CART', array('order', 'miniCart'), 'order/miniCartBlock');
		$this->addBlock('QUICK_LOGIN', 'quickLogin', 'user/block/quickLoginBlock');

		$this->application->logStat('Init FrontendController');

		return $initRes;
	}

	public function getRequestCurrency()
	{
		$currencyCode = $this->request->get('currency', $this->application->getDefaultCurrencyCode());

		// currency variable is sometimes POST'ed from external services, like payment gateway notifications
		if (!empty($_POST['currency']) && !empty($_GET['currency']))
		{
			$currencyCode = $_GET['currency'];
		}

		$currency = Currency::getValidInstanceById($currencyCode);
		if ($currency)
		{
			return $currency->getID();
		}
	}

	public function setOrder(CustomerOrder $order)
	{
		$this->order = $order;
	}

	public function addBreadCrumb($title, $url)
	{
		$this->breadCrumb[] = array('title' => $title, 'url' => $url);
	}

	protected function getCountryList()
	{
		$primaryCountries = str_replace(' ', '', strtoupper($this->config->get('PRIMARY_COUNTRIES')));

		if ($primaryCountries)
		{
			$defCountries = explode(',', $primaryCountries);
		}
		else
		{
			$defCountries = array($this->config->get('DEF_COUNTRY'));
		}

		$countries = $this->application->getEnabledCountries();
		asort($countries);

		// set default countries first
		$defCountries = array_reverse($defCountries);
		foreach ($defCountries as $country)
		{
			if (isset($countries[$country]))
			{
				$name = $countries[$country];
				unset($countries[$country]);
				$countries = array_merge(array($country => $name), $countries);
			}
		}

		return $countries;
	}

	protected function getStateList($country)
	{
		$states = State::getStatesByCountry($country);

		if ($states)
		{
			$states = array('' => '') + $states;
		}

		return $states;
	}

	protected function saveAddress(UserAddress $address, $prefix = '')
	{
		$address->loadRequestData($this->request, $prefix);
		$address->countryID->set($this->request->get($prefix . 'country'));
		$address->stateName->set($this->request->get($prefix . 'state_text'));
		if ($this->request->get($prefix . 'state_select'))
		{
			$address->state->set(State::getStateByIDAndCountry($this->request->get($prefix . 'state_select'), $this->request->get($prefix . 'country')));
		}
		else
		{
			$address->state->set(null);
		}
		$address->save();
	}

	public function quickLoginBlock()
	{
		if (!$this->user->isAnonymous())
		{
			return;
		}

		$this->loadLanguageFile('User');
		return new BlockResponse('return', $_SERVER['REQUEST_URI']);
	}

	protected function boxInformationMenuBlock()
	{
		ClassLoader::import('application.model.staticpage.StaticPage');
		$f = new ARSelectFilter(StaticPage::getIsInformationMenuCondition());
		$f->setOrder(new ARFieldHandle('StaticPage', 'position'));
		$response = new BlockResponse();
		$response->set('pages', StaticPage::createTree(ActiveRecordModel::getRecordSetArray('StaticPage', $f)));
		unset($f);
		return $response;
	}

	protected function boxNewsletterSubscribeBlock()
	{
		if (!$this->user->isAnonymous())
		{
			//return false;
		}

		ClassLoader::import('application.controller.NewsletterController');
		return new BlockResponse('form', new Form(NewsletterController::getSubscribeValidator()));
	}

	protected function boxShoppingCartBlock()
	{
		ClassLoader::import('application.model.order.SessionOrder');
		$response = new BlockResponse();

		$orderData = $this->session->get('orderData');
		if (!$orderData && $this->session->get('CustomerOrder'))
		{
			$orderData = SessionOrder::getOrderData();
		}

		$response->set('order', $orderData);
		$response->set('currency', $this->request->get('currency', $this->application->getDefaultCurrencyCode()));

		return $response;
	}

	protected function boxSwitchCurrencyBlock()
	{
		$returnRoute = $this->router->getRequestedRoute();
		$returnRoute = $this->router->createUrlFromRoute($returnRoute, true);
		$returnRoute = $this->router->addQueryParams($returnRoute);
		$returnRoute = $this->router->setUrlQueryParam($returnRoute, 'currency', '_curr_');

		$current = $this->getRequestCurrency();
		$currencies = $this->application->getCurrencySet();
		$currencyArray = array();
		foreach ($currencies as $currency)
		{
			$currencyArray[$currency->getID()] = $currency->toArray();
			$currencyArray[$currency->getID()]['url'] = str_replace('_curr_', $currency->getID(), $returnRoute);

			if ($currency->getID() == $current)
			{
				$currentCurrency = $currency->toArray();
			}
		}

		$response = new BlockResponse();
		$response->set('allCurrencies', $currencyArray);
		$response->set('currencies', array_diff_key($currencyArray, array($current => '')));
		$response->set('current', $current);
		$response->set('currentCurrency', $currentCurrency);
		return $response;
	}

	protected function boxSwitchCurrencyMenuBlock()
	{
		return $this->boxSwitchCurrencyBlock();
	}

	protected function boxLanguageSelectBlock()
	{
		$languages = $this->application->getLanguageSetArray(true, false);
		$current = $this->application->getLocaleCode();

		$returnRoute = $this->router->getRequestedRoute();

		if ('/' == substr($returnRoute, 2, 1))
		{
		  	$returnRoute = substr($returnRoute, 3);
		}

		$response = new BlockResponse();

		foreach ($languages as $key => $lang)
		{
			if ($lang['ID'] == $current)
			{
				$defKey = $key;
				$response->set('current', $lang);
			}
			else
			{
				// changing language from the index page
				if (strlen($returnRoute) == 2)
				{
					$returnRoute = '';
				}

				$langRoute = ($lang['isDefault'] ? '' : $lang['ID'] . '/') . $returnRoute;
				$languages[$key]['url'] = $this->router->addQueryParams($this->router->createUrlFromRoute($langRoute, true));
			}
		}

		$response->set('allLanguages', $languages);

		unset($languages[$defKey]);
		$response->set('languages', $languages);

		return $response;
	}

	protected function boxBreadCrumbBlock()
	{
		$home = array('controller' => 'index', 'action' => 'index');
		if ($this->locale->getLocaleCode() != $this->application->getDefaultLanguageCode())
		{
			$home['requestLanguage'] = $this->locale->getLocaleCode();
		}

		$root = array('title' => $this->config->get('STORE_NAME'),
					  'url' => $this->router->createUrl($home, true));

		if (reset($this->breadCrumb) != $root)
		{
			array_unshift($this->breadCrumb, $root);
		}

		$response = new BlockResponse();
		$response->set('breadCrumb', $this->breadCrumb);
		return $response;
	}

	protected function boxBreadCrumbTitleBlock()
	{
		$breadCrumbCopy = $this->breadCrumb;
		$last = array_pop($breadCrumbCopy);
		return new BlockResponse('breadCrumb', $last);
	}

	protected function boxSearchBlock()
	{
		ClassLoader::import('application.model.category.Category');

		$category = $this->getCategory();
		$search = $this->getCategory()->getPathNodeArray();

		$subCategories = $category->getSubCategoryArray();
		if ($subCategories)
		{
			if ($category->getID() != Category::ROOT_ID)
			{
				$search[] = $category->toArray();
			}

			$search = array_merge($search, $subCategories);
		}

		if (!$search)
		{
			$category = Category::getInstanceById(Category::ROOT_ID, Category::LOAD_DATA);
			$subCategories = $category->getSubCategorySet();

			foreach ($subCategories as $category)
			{
				$search[] = $category->toArray();
			}
		}

		$options = array(1 => $this->translate('_all_products'));

		foreach ($search as $cat)
		{
			if ($cat['isEnabled'])
			{
				$options[$cat['ID']] = $cat['name_lang'];
			}
		}

		$form = new Form($this->getValidator("productSearch", $this->request));
		$form->enableClientSideValidation(false);
		$form->set('id', $this->getCategory()->getID());
		$form->set('q', $this->request->get('q'));

		if ($this->filters && is_array($this->filters))
		{
			foreach ($this->filters as $filter)
			{
				if ($filter instanceof SearchFilter)
				{
					$form->set('q', $filter->getKeywords());
				}
			}
		}

		$response = new BlockResponse();
		$response->set('categories', $options);
		$response->set('form', $form);
		return $response;
	}

	private function getTopCategories()
	{
		ClassLoader::import('application.model.category.Category');

		if (!isset($this->topCategories))
		{
			$this->topCategories = Category::getInstanceByID(1)->getSubcategoryArray();
		}

		return $this->topCategories;
	}

	private function getTopCategoryId()
	{
		$this->getCurrentCategoryPath();
		return $this->topCategoryId;
	}

	private function getCurrentCategoryPath()
	{
		if (!isset($this->currentCategoryPath))
		{
			$currentCategory = $this->getCategory();

			// get path of the current category (except for top categories)
			if (!(1 == $currentCategory->getID()) && ($currentCategory->parentNode->get() && (1 < $currentCategory->parentNode->get()->getID())))
			{
				$path = $currentCategory->getPathNodeArray();

				$topCategoryId = $path[0]['ID'];
				unset($path[0]);
			}
			else
			{
				$topCategoryId = $this->getCategory()->getID();
			}

			$this->topCategoryId = $topCategoryId;
			$this->currentCategoryPath = $path;
		}

		return $this->currentCategoryPath;
	}

	protected function boxCategoryBlock()
	{
		ClassLoader::import('application.model.category.Category');

		// get top categories
		$topCategories = $this->getTopCategories();
		$path = $this->getCurrentCategoryPath();

		foreach ($topCategories as &$cat)
		{
		  	if ($this->topCategoryId == $cat['ID'])
		  	{
				$current =& $cat;
			}
		}

		$currentCategory = $this->getCategory();

		// get sibling (same-level) categories (except for top categories)
		if (!(1 == $currentCategory->getID()) && ($currentCategory->parentNode->get() && (1 < $currentCategory->parentNode->get()->getID())))
		{
			$siblings = $currentCategory->getSiblingArray();

			foreach ($path as &$node)
			{
			  	if ($node['ID'] != $this->getCategory()->getID())
			  	{
					$current['subCategories'] = array(0 => &$node);
				  	$current =& $node;
				}
				else
				{
					$current['subCategories'] =& $siblings;
					foreach ($current['subCategories'] as &$sib)
					{
					  	if ($sib['ID'] == $this->getCategory()->getID())
					  	{
							$current =& $sib;
						}
					}
				}
			}
		}

		// get subcategories of the current category (except for the root category)
		if ($this->getCategory()->getID() > 1)
		{
			$subcategories = $currentCategory->getSubcategorySet()->toArray();

			if ($subcategories)
			{
				$current['subCategories'] = $subcategories;
			}
		}

		$response = new BlockResponse();
		$response->set('categories', $topCategories);
		$response->set('currentId', $this->getCategory()->getID());
		$response->set('lang', 'en');
		return $response;
	}

	protected function dynamicCategoryMenuBlock()
	{
		$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle('Category', 'isEnabled'), true));
		$categories = ActiveRecordModel::getRecordSetArray('Category', Category::getRootNode()->getBranchFilter($f));

		$tree = array(1 => array('subCategories' => array()));
		foreach ($categories as $key => &$category)
		{
			$tree[$category['ID']] =& $category;
		}

		foreach ($categories as &$category)
		{
			$tree[$category['parentNodeID']]['subCategories'][] =& $category;
		}

		$tree = $tree[1]['subCategories'];

		$response = new BlockResponse('categories', $tree);

		$path = $this->getCategory()->getPathNodeArray();
		if ($path)
		{
			$response->set('topCategoryId', $path[0]['ID']);
		}

		$response->set('currentId', $this->getCategory()->getID());
		$response->set('currentCategory', $this->getCategory()->toArray());

		return $response;
	}

	protected function boxRootCategoryBlock()
	{
		ClassLoader::import('application.model.staticpage.StaticPage');

		if ($this->config->get('TOP_MENU_HIDE'))
		{
			return;
		}

		if (!$this->config->get('TOP_MENU_HIDE_CATS'))
		{
			$topCategories = $this->getTopCategories();
			$ids = array();
			foreach ($topCategories as $cat)
			{
				if ($cat['isEnabled'])
				{
					$ids[] = $cat['ID'];
				}
			}
			$f = new ARSelectFilter(new INCond(new ARFieldHandle('Category', 'parentNodeID'), $ids));
			$f->setOrder(new ARFieldHandle('Category', 'parentNodeID'));
			$f->setOrder(new ARFieldHandle('Category', 'lft'));
			$subCategories = array();
			foreach (ActiveRecordModel::getRecordSetArray('Category', $f) as $cat)
			{
				if ($cat['isEnabled'])
				{
					$subCategories[$cat['parentNodeID']][] = $cat;
				}
			}
		}

		$f = new ARSelectFilter(new IsNullCond(new ARFieldHandle('StaticPage', 'parentID')));
		$f->mergeCondition(StaticPage::getIsRootCategoriesMenuCondition());
		$f->setOrder(new ARFieldHandle('StaticPage', 'position'));
		$pages = ActiveRecordModel::getRecordSetArray('StaticPage', $f);
		$ids = array();
		$subPages = array();
		foreach($pages as $page)
		{
			$ids[] = $page['ID'];
		}
		$f = new ARSelectFilter(new INCond(new ARFieldHandle('StaticPage', 'parentID'), $ids));
		$f->setOrder(new ARFieldHandle('StaticPage', 'position'));
		foreach (ActiveRecordModel::getRecordSetArray('StaticPage', $f) as $page)
		{
			$subPages[$page['parentID']][] = $page;
		}

		if (empty($topCategories) && empty($pages))
		{
			return;
		}

		$response = new BlockResponse();
		$response->set('categories', $topCategories);
		$response->set('subCategories', $subCategories);
		$response->set('pages', $pages);
		$response->set('subPages', $subPages);
		$response->set('currentId', $this->getTopCategoryId());
		return $response;
	}

	protected function saleItemsBlock($useRoot = false)
	{
		ClassLoader::import('application.model.product.ProductFilter');

		$category = $useRoot ? Category::getRootNode() : $this->getCategory();
		$filter = new ProductFilter($category, new ARSelectFilter(new EqualsCond(new ARFieldHandle('Product', 'isFeatured'), true)));
		$filter->includeSubcategories();
		$filter->setEnabledOnly();

		$selectFilter = $filter->getSelectFilter();
		$selectFilter->setLimit($this->config->get('SALE_ITEMS_COUNT'));
		$selectFilter->setOrder(new ARExpressionHandle('RAND()'));

		$products = ActiveRecord::getRecordSetArray('Product', $selectFilter, array('Category', 'DefaultImage' => 'ProductImage'));

		ProductPrice::loadPricesForRecordSetArray($products);

		if ($products)
		{
			return new BlockResponse('products', $products);
		}
		else if (!$category->isRoot())
		{
			return $this->saleItemsBlock(true);
		}
	}

	protected function newestProductsBlock($useRoot = false)
	{
		ClassLoader::import('application.model.product.ProductFilter');

		$category = $useRoot ? Category::getRootNode() : $this->getCategory();
		$filter = new ProductFilter($category, new ARSelectFilter());
		$filter->includeSubcategories();
		$filter->setEnabledOnly();

		$selectFilter = $filter->getSelectFilter();
		$selectFilter->setLimit($this->config->get('NEWEST_ITEMS_COUNT'));
		$selectFilter->setOrder(new ARFieldHandle('Product', 'dateCreated'), 'DESC');

		$products = ActiveRecord::getRecordSetArray('Product', $selectFilter, array('Category', 'DefaultImage' => 'ProductImage'));

		ProductPrice::loadPricesForRecordSetArray($products);

		if ($products)
		{
			return new BlockResponse('products', $products);
		}
		else if (!$category->isRoot())
		{
			return $this->newestProductsBlock(true);
		}
	}

	public function bestsellingProductsBlock()
	{
		ClassLoader::import('application.model.product.ProductFilter');

		$cache = $this->application->getCache();
		$key = array('bestsellers', $this->getCategory()->getID() . '_' . $days);

		if (!$cache->get($key))
		{
			$category = $this->getCategory();
			$filter = new ProductFilter($category, new ARSelectFilter());
			$filter->includeSubcategories();
			$filter->setEnabledOnly();

			$selectFilter = $filter->getSelectFilter();
			$selectFilter->setLimit($this->config->get('BESTSELLING_ITEMS_COUNT'));
			$selectFilter->setOrder(new ARExpressionHandle('cnt'), 'DESC');

			$q = new ARSelectQueryBuilder();
			$q->includeTable('Product');
			$q->joinTable('Category', 'Product', 'ID', 'categoryID');
			$q->addField('Product.ID');
			$q->addField(new ARExpressionHandle('(SELECT SUM(count) FROM OrderedItem LEFT JOIN CustomerOrder ON OrderedItem.customerOrderID=CustomerOrder.ID WHERE productID=Product.ID AND CustomerOrder.isPaid=1 AND CustomerOrder.dateCompleted > "' . ARSerializableDateTime::createFromTimeStamp(strtotime('-' . $this->config->get('BESTSELLING_ITEMS_DAYS') . ' days')) . '")'), null, 'cnt');
			$q->setFilter($selectFilter);

			$cache->set($key, ActiveRecord::getDataByQuery($q));
		}

		$products = $cache->get($key);

		if (!$products)
		{
			return;
		}

		$ids = array();
		foreach ($products as $id)
		{
			$ids[] = $id['ID'];
		}

		$products = ActiveRecord::getRecordSetArray('Product', select(IN('Product.ID', $ids)) , array('DefaultImage' => 'ProductImage'));
		ProductPrice::loadPricesForRecordSetArray($products);

		if ($products)
		{
			return new BlockResponse('products', $products);
		}
	}

	protected function trackingBlock()
	{
		$code = array();

		if (!$this->config->get('TRACKING_SERVICES'))
		{
			return false;
		}

		foreach ($this->config->get('TRACKING_SERVICES') as $class => $enabled)
		{
			ClassLoader::import('library.tracking.method.' . $class);

			$data = array();

			foreach ($this->config->getSection('tracking/' . $class) as $key => $value)
			{
				$value = $this->config->get($key);
				$key = substr($key, strlen($class) + 1);
				$data[$key] = $value;
			}

			$tracker = new $class($data, $this);
			$code[$class] = $tracker->getHtml();
		}

		return new BlockResponse('code', $code);
	}

	public function latestNewsBlock()
	{
		$this->application->logStat('Starting latestNewsBlock');
		ClassLoader::import('application.model.sitenews.NewsPost');
		$f = new ARSelectFilter(new EqualsCond(new ARFieldHandle('NewsPost', 'isEnabled'), true));
		$f->setOrder(new ARFieldHandle('NewsPost', 'position'), 'DESC');
		$f->setLimit($this->config->get('NUM_NEWS_INDEX') + 1);

		$this->application->logStat('Before fetching news from DB');
		$news = array();
		$news = ActiveRecordModel::getRecordSetArray('NewsPost', $f);

		$this->application->logStat('Fetched news from DB');

		$response = new BlockResponse('news', $news);
		$response->set('isNewsArchive', count($news) > $this->config->get('NUM_NEWS_INDEX'));

		$this->application->logStat('Finished latestNewsBlock');
		return $response;
	}

	public function blockQuickNavBlock()
	{
		$response = new BlockResponse();

		// manufacturer list
		ClassLoader::import('application.controller.ManufacturersController');
		$controller = new ManufacturersController($this->application);
		$man = $controller->index();
		$response->set('manufacturers', $man->get('manufacturers'));
		$response->set('rootCat', $man->get('rootCat'));

		// category tree
		$cat = $this->dynamicCategoryMenuBlock();
		$response->set('categories', $cat->get('categories'));

		return $response;
	}

	/**
	 *  Recursively applies all selected filters to applicable categories
	 */
	private function applyFilters(&$categories, $parentFilterIds)
	{
		foreach ($categories as &$category)
		{
		  	$categoryFilters = $parentFilterIds;
			foreach ($this->filters as $filter)
		  	{
				if (!($filter instanceof SpecificationFilterInterface) ||
					$filter->getSpecField()->category->get()->getID() == $category['ID'])
				{
					$categoryFilters[$filter->getID()] = true;
				}

				if (isset($categoryFilters[$filter->getID()]))
				{
					$category['filters'][] = $filter;
				}
			}

			if (isset($category['subCategories']))
			{
			  	$this->applyFilters($category['subCategories'], $categoryFilters);
			}
		}
	}

	protected function ajaxResponse(CompositeJSONResponse $response)
	{
		$response->set('orderSummary', SessionOrder::getOrderData());

		if ($msg = $this->getMessage())
		{
			$response->set('successMessage', $msg);
		}

		if ($error = $this->getErrorMessage())
		{
			$response->set('errorMessage', $error);
		}

		return $response;
	}

	protected function getCategory()
	{
		$cat = Category::getRootNode();
		$cat->load();
		return $cat;
	}

	public function __get($name)
	{
		if ($inst = parent::__get($name))
		{
			return $inst;
		}

		switch ($name)
	  	{
			case 'order':
				ClassLoader::import('application.model.order.SessionOrder');
				$this->order = SessionOrder::getOrder();

				// check if order currency matches the request currency
				if (!$this->order->currency->get() || ($this->order->currency->get()->getID() != $this->getRequestCurrency()))
				{
					$this->order->changeCurrency(Currency::getInstanceByID($this->getRequestCurrency()));
				}

				return $this->order;
			break;

			default:
			break;
		}
	}

	public function __destruct()
	{
		if (isset($this->order))
		{
			//$this->order->__destruct();
			unset($this->order);
		}
	}

	public function toolbarBlock()
	{
		static $called = false;
		if ( $called)
		{
			// pp('<script>alert("called");</script>');
			return; // TODO: fix why from module controllers this block is called 2 times.
		}
		$called = true;
		$response = new BlockResponse();

		return $response;
	}
}

?>
