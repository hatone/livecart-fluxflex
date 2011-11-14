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

ClassLoader::import('application.model.ActiveRecordModel');
ClassLoader::import('application.model.category.Category');
ClassLoader::import('application.model.product.Product');

/**
 * Store entity presentation configuration (products, categories)
 *
 * @package application.model.product
 * @author Integry Systems <http://integry.com>
 */
class CategoryPresentation extends ActiveRecordModel
{
	public static function defineSchema($className = __CLASS__)
	{
		$schema = self::getSchemaInstance($className);
		$schema->setName($className);

		$schema->registerField(new ARPrimaryKeyField("ID", ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("categoryID", "Category", "ID", null, ARInteger::instance()));
		$schema->registerField(new ARForeignKeyField("productID", "Product", "ID", null, ARInteger::instance()));

		$schema->registerField(new ARField("isSubcategories", ARBool::instance()));
		$schema->registerField(new ARField("isVariationImages", ARBool::instance()));
		$schema->registerField(new ARField("isAllVariations", ARBool::instance()));
		$schema->registerField(new ARField("theme", ARVarchar::instance(20)));
		$schema->registerField(new ARField("listStyle", ARVarchar::instance(20)));

		return $schema;
	}

	public static function getInstance(ActiveRecordModel $parent)
	{
		$parentClass = get_class($parent);
		$set = $parent->getRelatedRecordSet(__CLASS__, new ARSelectFilter(), array($parentClass));
		if ($set->size())
		{
			return $set->get(0);
		}
		else
		{
			return self::getNewInstance($parent);
		}
	}

	public function getTheme()
	{
		return $this->theme->get();
	}

	public static function getNewInstance(ActiveRecordModel $parent)
	{
		$instance = parent::getNewInstance(__CLASS__);
		if ($parent instanceof Category)
		{
			$instance->category->set($parent);
		}
		else
		{
			$instance->product->set($parent);
		}

		return $instance;
	}

	public static function getThemeByCategory(Category $category)
	{
		$f = new ARSelectFilter(self::getCategoryCondition($category));
		self::setCategoryOrder($category, $f);

		$set = ActiveRecordModel::getRecordSet(__CLASS__, $f, array('Category'));
		return self::getInheritedConfig($set);
	}

	public static function getThemeByProduct(Product $product)
	{
		$c = eq(__CLASS__ . '.productID', $product->getID());
		$c->addOr(self::getCategoryCondition($product->getCategory()));
		$f = select($c);
		$f->setOrder(new ARExpressionHandle('CategoryPresentation.productID=' . $product->getID()), 'DESC');
		self::setCategoryOrder($product->getCategory(), $f);

		// check if a theme is defined for this product particularly
		$set = ActiveRecordModel::getRecordSet(__CLASS__, $f, array('Category'));
		return self::getInheritedConfig($set);
	}

	private function getInheritedConfig(ARSet $set)
	{
		if ($set->size())
		{
			// category level configuration?
			$prod = $set->shift();

			// fill missing product level settings with category level settings
			foreach ($set as $cat)
			{
				foreach (array('theme', 'isAllVariations', 'isVariationImages', 'listStyle') as $field)
				{
					if (!$prod->$field->get())
					{
						$prod->$field->set($cat->$field->get());
					}
				}
			}

			return $prod;
		}
	}

	private static function getCategoryCondition(Category $category)
	{
		$own = new EqualsCond(new ARFieldHandle(__CLASS__, 'categoryID'), $category->getID());
		$parent = new EqualsOrLessCond(new ARFieldHandle('Category', 'lft'), $category->lft->get());
		$parent->addAND(new EqualsOrMoreCond(new ARFieldHandle('Category', 'rgt'), $category->rgt->get()));
		$parent->addAND(new EqualsCond(new ARFieldHandle(__CLASS__, 'isSubcategories'), true));
		$own->addOR($parent);

		return $own;
	}

	private static function setCategoryOrder(Category $category, ARSelectFilter $f)
	{
		$f->setOrder(new ARExpressionHandle('CategoryPresentation.categoryID=' . $category->getID()), 'DESC');
		$f->setOrder(new ARFieldHandle('Category', 'lft'), 'DESC');
	}
}

?>
