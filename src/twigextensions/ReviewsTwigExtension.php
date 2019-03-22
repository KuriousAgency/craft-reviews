<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * Product & Store Reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\twigextensions;

use kuriousagency\reviews\Reviews;

use Craft;

/**
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class ReviewsTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Reviews';
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('averageRating', [$this, 'getAverageRating']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            //new \Twig_SimpleFunction('averageRating', [$this, 'getAverageRating']),
        ];
    }

    public function getAverageRating($reviews = [], $halfRounding=false)
    {
		$ratings = [];
		
		foreach ($reviews as $review)
		{
			$ratings[] = $review->rating;
		}

		if (!count($ratings)) {
			return null;
		}

		$average = array_sum($ratings)/count($ratings);

		if ($halfRounding) {
			return floor($average * 2) / 2;
		}
		return round($average);
    }
}
