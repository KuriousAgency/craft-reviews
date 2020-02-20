<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\controllers;

use kuriousagency\reviews\Reviews;
use kuriousagency\reviews\elements\Review;

use craft\commerce\Plugin as Commerce;

use Craft;
use craft\web\Controller;
use yii\base\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['save', 'submit'];

    // Public Methods
    // =========================================================================

	public function actionIndex(): Response
	{
		return $this->renderTemplate('reviews/index');
	}

	public function actionEdit(int $id = null, string $purchasableType = null): Response
	{
		if ($id) {
			$review = Reviews::$plugin->service->getReviewById($id);
			if (!$review) {
				throw new Exception(Craft::t('reviews', 'No review exists with the ID “{id}”.', ['id' => $id]));
			}
		} else {
			$review = new Review();
			if ($purchasableType) {
				$arr = explode('-',$purchasableType);
				$lastString = ucfirst(array_pop($arr));
				array_push($arr,$lastString);
				$review->purchasableType = implode('\\',$arr);
			}
		}

		return $this->renderTemplate('reviews/edit', [
			'review' => $review,
		]);
	}

	public function actionSubmit()
	{
		$this->requirePostRequest();

		$request = Craft::$app->getRequest();

		$number = $request->getRequiredBodyParam('orderNumber');
		$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);

		$reviews = $request->getRequiredBodyParam('reviews');
		$errors = [];
		$models = [];

		foreach ($reviews as $review)
		{
			$newReview = new Review();
			$newReview->feedback = $review['feedback'];
			$newReview->rating = $review['rating'];
			$newReview->customerId = $order->customer->id;
			$newReview->purchasableId = $review['purchasableId'];
			// Now that purchasableId is finished being set lets get the type
			if ($newReview->purchasableId) {
				$newReview->purchasableType = get_class(Commerce::getInstance()->getPurchasables()->getPurchasableById($review->purchasableId));
			}
			$newReview->purchasableType = get_class(Commerce::getInstance()->getPurchasables()->getPurchasableById($review->purchasableId));

			$newReview->orderId = $order->id;

			$models[] = $newReview;

			if (!$newReview->validate()) {
				$errors[] = $newReview->getErrors();
			}
		}

		if (count($errors)) {
			Craft::$app->getUrlManager()->setRouteParams([
                'reviews' => $models
			]);

			return null;
		}

		foreach ($models as $review)
		{
			Craft::$app->getElements()->saveElement($review, false);
		}

		return $this->redirectToPostedUrl($order);

	}
	
	public function actionSave()
	{
		$this->requirePostRequest();

		$request = Craft::$app->getRequest();
		
		$reviewId = $request->getBodyParam('reviewId');
		if ($reviewId) {
			$review = Reviews::$plugin->service->getReviewById($reviewId);

			if (!$review) {
				throw new Exception(Craft::t('reviews', 'No review exists with the ID “{id}”.', ['id' => $reviewId]));
			}
		} else {
			$review = new Review();
		}

		$review->feedback = $request->getBodyParam('feedback', $review->feedback);
		$review->reply = $request->getBodyParam('reply', $review->reply);
		$review->rating = $request->getBodyParam('rating', $review->rating);
		$review->email = $request->getBodyParam('email', $review->email);
		$review->firstName = $request->getBodyParam('firstName', $review->firstName);
		$review->lastName = $request->getBodyParam('lastName', $review->lastName);
		$review->enabled = $request->getBodyParam('enabled', $review->enabled);
		$review->customerId = $request->getBodyParam('customerId', $review->customerId);
		$review->purchasableId = $request->getBodyParam('purchasableId', $review->purchasableId);
		$review->orderId = $request->getBodyParam('orderId', $review->orderId);

		if (!$review->customerId && $request->isSiteRequest) {
			if ($user = Craft::$app->getUser()->getIdentity()) {
				$customer = Commerce::getInstance()->getCustomers()->getCustomerByUserId($user->id);
				$review->customerId = $customer->id;
			} else if ($user = Craft::$app->getUsers()->getUserByUsernameOrEmail($review->email)) {
				$customer = Commerce::getInstance()->getCustomers()->getCustomerByUserId($user->id);
				$review->customerId = $customer->id;
			}
		}

		if (is_array($review->purchasableId)) {
			$review->purchasableId = $review->purchasableId[0];
		}
		if (is_array($review->orderId)) {
			$review->orderId = $review->orderId[0];
		}

		// Now that purchasableId is finished being set lets get the type
		if ($review->purchasableId) {
			$review->purchasableType = get_class(Commerce::getInstance()->getPurchasables()->getPurchasableById($review->purchasableId));
		}
		// Craft::dd($review);

		if (!Craft::$app->getElements()->saveElement($review)) {
			Craft::$app->getSession()->setError(Craft::t('reviews', 'Couldn’t save review.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'review' => $review
			]);
			
			return null;
		}

		Craft::$app->getSession()->setNotice(Craft::t('reviews', 'Review saved.'));

        return $this->redirectToPostedUrl($review);
	}

	public function actionDelete()
	{
		$this->requirePostRequest();

		$reviewId = Craft::$app->getRequest()->getRequiredParam('reviewId');
		$review = Reviews::$plugin->service->getReviewById($reviewId);

		if (!$review) {
			throw new Exception(Craft::t('reviews', 'No review exists with the ID “{id}”.', ['id' => $reviewId]));
		}

		if (!Craft::$app->getElements()->deleteElement($review)) {
			if (Craft::$app->getRequest()->getAcceptsJson()) {
                return $this->asJson(['success' => false]);
            }

            Craft::$app->getSession()->setError(Craft::t('reviews', 'Couldn’t delete review.'));
            Craft::$app->getUrlManager()->setRouteParams([
                'review' => $review
			]);
			
			return null;
		}

		if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson(['success' => true]);
        }

        Craft::$app->getSession()->setNotice(Craft::t('reviews', 'Review deleted.'));
        return $this->redirectToPostedUrl($review);
	}
}
