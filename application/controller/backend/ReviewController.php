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

ClassLoader::import('application.controller.backend.abstract.ActiveGridController');
ClassLoader::import('application.model.category.Category');
ClassLoader::import('application.model.product.Product');
ClassLoader::import('application.model.product.ProductReview');

/**
 *
 * @package application.controller.backend
 * @author Integry Systems
 * @role product
 */
class ReviewController extends ActiveGridController
{
	public function index()
	{
		$response = $this->getGridResponse();
		$response->set('id', ($this->isCategory() ? 'c' : '') . $this->getID());
		$response->set('container', $this->request->get('category') ? 'tabReviews' : 'tabProductReviews');
		return $response;
	}

	public function edit()
	{
		$review = ActiveRecordModel::getInstanceById('ProductReview', $this->request->get('id'), ProductReview::LOAD_DATA, array('Product'));
		//$manufacturer->getSpecification();

		$response = new ActionResponse('review', $review->toArray());
		$form = $this->buildForm($review);
		$form->setData($review->toArray());

		// get ratings
		foreach ($review->getRelatedRecordSetArray('ProductRating', new ARSelectFilter()) as $rating)
		{
			$form->set('rating_' . $rating['ratingTypeID'], $rating['rating']);
		}

		$form->set('rating_', $review->rating->get());

		//$manufacturer->getSpecification()->setFormResponse($response, $form);
		$response->set('form', $form);
		$response->set('ratingTypes', ProductRatingType::getProductRatingTypes($review->product->get())->toArray());

		$options = range(1, $this->config->get('RATING_SCALE'));
		$response->set('ratingOptions', array_combine($options, $options));

		return $response;
	}

	public function update()
	{
		$review = ActiveRecordModel::getInstanceById('ProductReview', $this->request->get('id'), ProductReview::LOAD_DATA, array('Product'));
		$validator = $this->buildValidator($review);

		if ($validator->isValid())
		{
			$review->loadRequestData($this->request);
			$review->save();

			// set ratings
			foreach ($review->getRelatedRecordSet('ProductRating', new ARSelectFilter()) as $rating)
			{
				$typeId = $rating->ratingType->get() ? $rating->ratingType->get()->getID() : '';
				$rating->rating->set($this->request->get('rating_' . $typeId));
				$rating->save();
			}

			return new JSONResponse(array('review' => $review->toFlatArray()), 'success', $this->translate('_review_was_successfully_saved'));
		}
		else
		{
			return new JSONResponse(array('errors' => $validator->getErrorList()), 'failure');
		}
	}

	public function changeColumns()
	{
		parent::changeColumns();
		return $this->getGridResponse();
	}

	private function getGridResponse()
	{
		$this->loadLanguageFile('backend/Category');

		$response = new ActionResponse();
		$this->setGridResponse($response);
		return $response;
	}

	protected function getClassName()
	{
		return 'ProductReview';
	}

	protected function getCSVFileName()
	{
		return 'reviews.csv';
	}

	protected function getDefaultColumns()
	{
		return array('ProductReview.ID', 'ProductReview.title', 'Product.name', 'ProductReview.nickname', 'ProductReview.dateCreated', 'ProductReview.isEnabled');
	}

	protected function getDisplayedColumns()
	{
		return parent::getDisplayedColumns(null, array('Product.ID' => 'numeric'));
	}

	public function getAvailableColumns()
	{
		$availableColumns = parent::getAvailableColumns();

		unset($availableColumns['ProductReview.ratingSum']);
		unset($availableColumns['ProductReview.ratingCount']);
		unset($availableColumns['ProductReview.ip']);
		unset($availableColumns['Product.ID']);

		return $availableColumns;
	}

	protected function getCustomColumns()
	{
		if ($this->isCategory())
		{
			$availableColumns['Product.name'] = 'text';
			$availableColumns['Product.ID'] = 'numeric';

			return $availableColumns;
		}

		return array();
	}

	protected function setDefaultSortOrder(ARSelectFilter $filter)
	{
		$filter->setOrder(new ARFieldHandle($this->getClassName(), 'ID'), 'DESC');
	}

	protected function getSelectFilter()
	{
		$id = $this->getID();

		if ($this->isCategory())
		{
			$owner = Category::getInstanceByID($id, Category::LOAD_DATA);

			$cond = new EqualsOrMoreCond(new ARFieldHandle('Category', 'lft'), $owner->lft->get());
			$cond->addAND(new EqualsOrLessCond(new ARFieldHandle('Category', 'rgt'), $owner->rgt->get()));
		}
		else
		{
			$cond = new EqualsCond(new ARFieldHandle('ProductReview', 'productID'), $id);
		}

		return new ARSelectFilter($cond);
	}

	private function isCategory()
	{
		$id = array_pop(explode('_', $this->request->get('id')));
		return (substr($id, 0, 1) == 'c') || $this->request->get('category');
	}

	private function getID()
	{
		$id = array_pop(explode('_', $this->request->get('id')));

		if ($this->isCategory() && (substr($id, 0, 1) == 'c'))
		{
			$id = substr($id, 1);
		}

		return $id;
	}

	protected function getReferencedData()
	{
		return array('Product', 'Category');
	}

	private function buildValidator(ProductReview $review)
	{
		$validator = $this->getValidator("productRating", $this->getRequest());

		// option validation
		foreach (ProductRatingType::getProductRatingTypes($review->product->get())->toArray() as $type)
		{
			$validator->addCheck('rating_' . $type['ID'], new IsNotEmptyCheck($this->translate('_err_no_rating_selected')));
		}

		$validator->addCheck('nickname', new IsNotEmptyCheck($this->translate('_err_no_review_nickname')));
		$validator->addCheck('title', new IsNotEmptyCheck($this->translate('_err_no_review_summary')));
		$validator->addCheck('text', new IsNotEmptyCheck($this->translate('_err_no_review_text')));

		return $validator;
	}

	/**
	 * Builds a category form instance
	 *
	 * @return Form
	 */
	private function buildForm(ProductReview $review)
	{
		return new Form($this->buildValidator($review));
	}
}

?>
