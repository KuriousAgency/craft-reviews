<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\assetbundles\productreviewsfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class ProductReviewsFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@kuriousagency/reviews/assetbundles/productreviewsfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/ProductReviews.js',
        ];

        $this->css = [
            'css/ProductReviews.css',
        ];

        parent::init();
    }
}
