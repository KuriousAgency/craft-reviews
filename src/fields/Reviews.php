<?php
namespace kuriousagency\reviews\fields;

use kuriousagency\reviews\elements\Review;

use Craft;
use craft\fields\BaseRelationField;

class Reviews extends BaseRelationField
{
    // Public Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('reviews', 'Reviews');
    }

    protected static function elementType(): string
    {
        return Review::class;
    }

    public static function defaultSelectionLabel(): string
    {
        return Craft::t('reviews', 'Add a Review');
    }
}