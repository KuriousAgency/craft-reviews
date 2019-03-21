<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\widgets;

use kuriousagency\reviews\Reviews;
use kuriousagency\reviews\assetbundles\reviewswidgetwidget\ReviewsWidgetWidgetAsset;

use Craft;
use craft\base\Widget;

/**
 * Reviews Widget
 *
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class ReviewsWidget extends Widget
{

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $message = 'Hello, world.';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('reviews', 'ReviewsWidget');
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias("@kuriousagency/reviews/assetbundles/reviewswidgetwidget/dist/img/ReviewsWidget-icon.svg");
    }

    /**
     * @inheritdoc
     */
    public static function maxColspan()
    {
        return null;
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            [
                ['message', 'string'],
                ['message', 'default', 'value' => 'Hello, world.'],
            ]
        );
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'reviews/_components/widgets/ReviewsWidget_settings',
            [
                'widget' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml()
    {
        Craft::$app->getView()->registerAssetBundle(ReviewsWidgetWidgetAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'reviews/_components/widgets/ReviewsWidget_body',
            [
                'message' => $this->message
            ]
        );
    }
}
