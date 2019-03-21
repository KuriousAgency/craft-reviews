<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\services;

use kuriousagency\reviews\Reviews;
use kuriousagency\reviews\elements\Review;

use Craft;
use craft\base\Component;

/**
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class ReviewsService extends Component
{
    // Public Methods
    // =========================================================================

	public function getReviewById(int $id, $siteId = null)
	{
		$review = Craft::$app->getElements()->getElementById($id, Review::class, $siteId);

		return $review;
	}
}
