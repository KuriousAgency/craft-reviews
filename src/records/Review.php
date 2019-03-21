<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\records;

use kuriousagency\reviews\Reviews;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class Review extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reviews}}';
    }
}
