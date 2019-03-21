<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\elements\db;

use kuriousagency\reviews\Reviews;

use Craft;
use craft\base\Element;
use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\elements\db\ElementQuery;
use craft\commerce\Plugin as Commerce;
use craft\elements\User;
use craft\commerce\elements\Product;
use craft\commerce\elements\Order;
use craft\helpers\ArrayHelper;
use craft\helpers\Db;
use DateTime;
use yii\db\Connection;
use yii\db\Expression;


class ReviewQuery extends ElementQuery
{
    // Properties
    // =========================================================================

	public $feedback;
	
	public $reply;

	public $rating; 

	public $customerId;
	
	public $productId;

	public $orderId;

	public $email;

	public $firstName;

	public $lastName;

	public $enabled;

	public $dateCreated;

	public $dateUpdated;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct(string $elementType, array $config = [])
    {
        // Default orderBy
        if (!isset($config['orderBy'])) {
            $config['orderBy'] = 'reviews.dateCreated DESC';
        }

        parent::__construct($elementType, $config);
    }

    /**
     * @inheritdoc
     */
    /*public function __set($name, $value)
    {
        switch ($name) {
            case 'createdAfter':
                $this->createdAfter($value);
				break;
			case 'createdBefore':
                $this->createdBefore($value);
                break;
            default:
                parent::__set($name, $value);
        }
	}*/
	
	public function rating($value = null)
	{
		$this->rating = $value;
		return $this;
	}

	public function customerId($value = null)
	{
		$this->customerId = $value;
		return $this;
	}

	public function productId($value = null)
	{
		$this->productId = $value;
		return $this;
	}

	public function orderId($value = null)
	{
		$this->orderId = $value;
		return $this;
	}

	public function email($value = null)
	{
		$this->email = $value;
		return $this;
	}

	public function enabled($value = null)
	{
		$this->enabled = $value;
		return $this;
	}

	public function customer($value = null)
	{
		if ($value) {
            $this->customerId = $value->id;
        } else {
            $this->customerId = null;
        }

        return $this;
	}

	public function user($value)
    {
        if ($value instanceof User) {
            $customer = Commerce::getInstance()->getCustomers()->getCustomerByUserId($value->id);
            $this->customerId = $customer->id ?? null;
        } else if ($value !== null) {
            $customer = Commerce::getInstance()->getCustomers()->getCustomerByUserId($value);
            $this->customerId = $customer->id ?? null;
        } else {
            $this->customerId = null;
        }

        return $this;
	}
	
	public function product($value)
	{
		if ($value instanceof Product) {
			$product = Commerce::getInstance()->getProducts()->getProductById($value->id);
			$this->productId = $product->id ?? null;
		} else if ($value !== null) {
			$product = Commerce::getInstance()->getProducts()->getProductById($value);
			$this->productId = $product->id ?? null;
		} else {
			$this->productId = null;
		}

		return $this;
	}

	public function commerceOrder($value)
	{
		if ($value instanceof Order) {
			$order = Commerce::getInstance()->getOrders()->getOrderById($value->id);
			$this->orderId = $order->id ?? null;
		} else if ($value !== null) {
			$order = Commerce::getInstance()->getOrders()->getOrderById($value);
			$this->orderId = $order->id ?? null;
		} else {
			$this->orderId = null;
		}

		return $this;
	}



    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function beforePrepare(): bool
    {

        $this->joinElementTable('reviews');

        $this->query->select([
			'reviews.id',
			'reviews.feedback',
			'reviews.reply',
			'reviews.rating',
			'reviews.customerId',
			'reviews.productId',
			'reviews.orderId',
			'reviews.email',
			'reviews.firstName',
			'reviews.lastName',
			'reviews.enabled',
			'reviews.dateCreated',
			'reviews.dateUpdated',
        ]);

		if ($this->rating) {
            $this->subQuery->andWhere(Db::parseParam('reviews.rating', $this->rating));
        }
		
		if ($this->email) {
            $this->subQuery->andWhere(Db::parseParam('reviews.email', $this->email));
		}
		
		if ($this->firstName) {
            $this->subQuery->andWhere(Db::parseParam('reviews.firstName', $this->firstName));
		}
		
		if ($this->lastName) {
            $this->subQuery->andWhere(Db::parseParam('reviews.lastName', $this->lastName));
		}
		
		if ($this->enabled) {
            $this->subQuery->andWhere(Db::parseParam('reviews.enabled', $this->enabled));
		}
		
		if ($this->dateCreated) {
            $this->subQuery->andWhere(Db::parseDateParam('reviews.dateCreated', $this->dateCreated));
		}
		
		if ($this->dateUpdated) {
            $this->subQuery->andWhere(Db::parseDateParam('reviews.dateUpdated', $this->dateUpdated));
		}
		
		if ($this->customerId) {
            $this->subQuery->andWhere(Db::parseParam('reviews.customerId', $this->customerId));
		}
		
		if ($this->productId) {
            $this->subQuery->andWhere(Db::parseParam('reviews.productId', $this->productId));
		}
		
		if ($this->orderId) {
            $this->subQuery->andWhere(Db::parseParam('reviews.orderId', $this->orderId));
		}
		
		return parent::beforePrepare();
    }


}
