<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\variables;

use kuriousagency\reviews\Reviews;
use kuriousagency\reviews\elements\Review;
use kuriousagency\reviews\elements\db\ReviewQuery;

use craft\commerce\elements\Product;

use Craft;
use yii\base\Behavior;

/**
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class ReviewsVariable extends Behavior
{
	
	//public $reviews;
	
	// Public Methods
	// =========================================================================
	
	public function init()
	{
		parent::init();

		//$this->reviews = Reviews::$plugin;
	}

	public function reviews($criteria = null): ReviewQuery
	{
		$query = Review::find();
		if ($criteria) {
			Craft::configure($query, $criteria);
		}
		return $query;
	}

}
