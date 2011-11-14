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

ClassLoader::import('application.model.product.Product');
ClassLoader::import('application.model.category.Category');
ClassLoader::import('application.model.system.MultilingualObject');
ClassLoader::import('application.model.product.ProductOptionChoice');

/**
 * Configurable product options
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class ProductOption extends MultilingualObject
{
	const TYPE_BOOL = 0;
	const TYPE_SELECT = 1;
	const TYPE_TEXT = 2;
	const TYPE_FILE = 3;

	const DISPLAYTYPE_SELECTBOX = 0;
	const DISPLAYTYPE_RADIO = 1;

	protected $choices = array();

	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName("ProductOption");

		$schema->registerCircularReference('DefaultChoice', 'ProductOptionChoice');

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("productID", "Product", "ID", null, ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("categoryID", "Category", "ID", null, ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("defaultChoiceID", "ProductOptionChoice", "ID", "ProductOptionChoice", ARInteger::instance()));

		$schema->registerField(new ARField("name", ARArray::instance()));
		$schema->registerField(new ARField("description", ARArray::instance()));
		$schema->registerField(new ARField("selectMessage", ARArray::instance()));
		$schema->registerField(new ARField("type", ARInteger::instance(4)));
		$schema->registerField(new ARField("displayType", ARInteger::instance(4)));
		$schema->registerField(new ARField("isRequired", ARBool::instance()));
		$schema->registerField(new ARField("isDisplayed", ARBool::instance()));
		$schema->registerField(new ARField("isDisplayedInList", ARBool::instance()));
		$schema->registerField(new ARField("isDisplayedInCart", ARBool::instance()));
		$schema->registerField(new ARField("isPriceIncluded", ARBool::instance()));
		$schema->registerField(new ARField("position", ARInteger::instance(4)));
		$schema->registerField(new ARField("maxFileSize", ARInteger::instance(4)));
		$schema->registerField(new ARField("fileExtensions", ARVarchar::instance(100)));
	}

	/**
	 * Creates a new option instance
	 *
	 * @param Category $category
	 *
	 * @return Product
	 */
	public static function getNewInstance(ActiveRecordModel $parent)
	{
		$option = parent::getNewInstance(__CLASS__);

		if ($parent instanceof Product)
		{
			$option->product->set($parent);
		}
		else if ($parent instanceof Category)
		{
			$option->category->set($parent);
		}
		else
		{
			throw new ApplicationException('ProductOption parent must be either Product or Category');
		}

		return $option;
	}

	/**
	 * Get ActiveRecord instance
	 *
	 * @param mixed $recordID
	 * @param bool $loadRecordData
	 * @param bool $loadReferencedRecords
	 *
	 * @return Product
	 */
	public static function getInstanceByID($recordID, $loadRecordData = false, $loadReferencedRecords = false)
	{
		return parent::getInstanceByID(__CLASS__, $recordID, $loadRecordData, $loadReferencedRecords);
	}

	/**
	 * Get products record set
	 *
	 * @param ARSelectFilter $filter
	 * @param bool $loadReferencedRecords
	 *
	 * @return ARSet
	 */
	public static function getRecordSet(ARSelectFilter $filter, $loadReferencedRecords = false)
	{
		return parent::getRecordSet(__CLASS__, $filter, $loadReferencedRecords);
	}

	/*####################  Value retrieval and manipulation ####################*/

	public function isBool()
	{
		return $this->type->get() == self::TYPE_BOOL;
	}

	public function isText()
	{
		return $this->type->get() == self::TYPE_TEXT;
	}

	public function isSelect()
	{
		return $this->type->get() == self::TYPE_SELECT;
	}

	public function isFile()
	{
		return $this->type->get() == self::TYPE_FILE;
	}

	public function addChoice(ProductOptionChoice $choice)
	{
		$this->choices[$choice->getID()] = $choice;
	}

	public function getChoiceByID($id)
	{
		$s = $this->getRelatedRecordSet('ProductOptionChoice', new ARSelectFilter(new EqualsCond(new ARFieldHandle('ProductOptionChoice', 'ID'), $id)));
		if ($s->size())
		{
			return $s->get(0);
		}
	}

	public static function getProductOptions(Product $product)
	{
		$options = $product->getRelatedRecordSet('ProductOption');
		self::loadChoicesForRecordSet($options);
		return $options;
	}

	public static function loadOptionsForProductSet(ARSet $products)
	{
		// load category options
		$f = new ARSelectFilter();

		$categories = $productIDs = array();
		foreach ($products as $product)
		{
			foreach ($product->getAllCategories() as $cat)
			{
				$categories[$cat->getID()] = $cat;
			}

			$productIDs[] = $product->getID();
			if ($product->parent->get())
			{
				$productIDs[] = $product->parent->get()->getID();
			}
		}

		foreach ($categories as $category)
		{
			if($category->isLoaded() == false)
			{
				$category->load();
			}
			$c = new EqualsOrLessCond(new ARFieldHandle('Category', 'lft'), $category->lft->get());
			$c->addAND(new EqualsOrMoreCond(new ARFieldHandle('Category', 'rgt'), $category->rgt->get()));

			if (!isset($categoryCond))
			{
				$categoryCond = $c;
			}
			else
			{
				$categoryCond->addOR($c);
			}
		}

		// product options
		$productCond = new INCond(new ARFieldHandle('ProductOption', 'productID'), $productIDs);
		if (!isset($categoryCond))
		{
			$categoryCond = $productCond;
		}
		else
		{
			$categoryCond->addOR($productCond);
		}

		$f->setCondition($categoryCond);

		// ordering
		$f->setOrder(new ARFieldHandle('ProductOption', 'productID'), 'DESC');
		$f->setOrder(new ARFieldHandle('Category', 'lft'), 'DESC');
		$f->setOrder(new ARFieldHandle('ProductOption', 'position'), 'DESC');

		
		$options = ProductOption::getRecordSet($f, array('DefaultChoice' => 'ProductOptionChoice', 'Category'));

		self::loadChoicesForRecordSet($options);

		// sort by products
		$sorted = array();
		foreach ($products as $product)
		{
			foreach ($options as $index => $option)
			{
				if ($option->product->get() && (($option->product->get()->getID() == $product->getID()) || ($product->parent->get() && ($option->product->get()->getID() == $product->parent->get()->getID()))))
				{
					$sorted[$product->getID()][] = $option;
				}

				if ($option->category->get())
				{
					$option->category->get()->load();
					foreach ($product->getAllCategories() as $category)
					{
						if ($option->category->get()->isAncestorOf($category))
						{
							$sorted[$product->getID()][] = $option;
							break;
						}
					}
				}
			}
		}
		return $sorted;
	}

	public static function loadChoicesForRecordSet(ARSet $options)
	{
		$ids = $refs = array();

		// load select option choices
		foreach ($options as $option)
		{
			if ($option->isSelect())
			{
				$ids[] = $option->getID();
				$refs[$option->getID()] = $option;
			}
		}

		if ($ids)
		{
			$f = new ARSelectFilter(new INCond(new ARFieldHandle('ProductOptionChoice', 'optionID'), $ids));
			$f->setOrder(new ARFieldHandle('ProductOptionChoice', 'position'));

			foreach (ActiveRecordModel::getRecordSet('ProductOptionChoice', $f) as $choice)
			{
				$refs[$choice->option->get()->getID()]->addChoice($choice);
			}
		}
	}

	public static function includeProductPrice(Product $product, &$options)
	{
		$prices = $product->getPricingHandler()->toArray();
		$prices = $prices['calculated'];
		foreach ($options as &$option)
		{
			if (!empty($option['choices']))
			{
				foreach ($option['choices'] as &$choice)
				{
					foreach ($prices as $currency => $price)
					{
						$instance = Currency::getInstanceByID($currency);
						$choice['formattedTotalPrice'][$currency] = $instance->getFormattedPrice($price + $instance->convertAmountFromDefaultCurrency($choice['priceDiff']));
					}
				}
			}
		}
	}

	public static function getFileExtensions($extensionString)
	{
		$s = trim(preg_replace('/[^ a-z0-9]/', '', strtolower($extensionString)));
		$extensions = array();
		foreach (explode(' ', $s) as $ext)
		{
			$extensions[] = trim($ext);
		}

		return $extensions;
	}

	/*####################  Saving ####################*/

	protected function insert()
	{
	  	$this->setLastPosition();

		parent::insert();
	}

	public static function deleteByID($id)
	{
		return parent::deleteByID(__class__, $id);
	}

	/*####################  Data array transformation ####################*/

	public function toArray()
	{
		$array = parent::toArray();

	  	if ($this->choices)
	  	{
	  		$array['choices'] = array();

	  		foreach ($this->choices as $choice)
	  		{
	  			$array['choices'][] = $choice->toArray();
			}
		}

		$this->setArrayData($array);

	  	return $array;
	}

	/*####################  Get related objects ####################*/

	public function getChoiceSet()
	{
		$f = new ARSelectFilter();
		$f->setOrder(new ARFieldHandle('ProductOptionChoice', 'position'));

		return $this->getRelatedRecordSet('ProductOptionChoice', $f);
	}

	public function __clone()
	{
		parent::__clone();

		$this->choices = null;
		$this->save();

		$defaultChoice = $this->originalRecord->defaultChoice->get();

		foreach ($this->originalRecord->getChoiceSet() as $choice)
		{
			$newChoice = clone $choice;
			$newChoice->option->set($this);
			$newChoice->save();

			if ($defaultChoice && ($choice->getID() == $defaultChoice->getID()))
			{
				$this->defaultChoice->set($newChoice);
			}
		}

		$this->save();
	}
}

?>
