<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\elements\actions;

use kuriousagency\reviews\Reviews;

use Craft;
use craft\base\ElementAction;
use craft\elements\actions\Delete;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Json;

/**
 * Class Disable
 *
 * @property null|string $triggerHtml the action’s trigger HTML
 * @property string $triggerLabel the action’s trigger label
 */

class DeleteReview extends Delete
{
    // Public Properties
    // =========================================================================


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function performAction(ElementQueryInterface $query = null): bool
    {
        if (!$query) {
            return false;
        }

        foreach ($query->all() as $review) {
            Craft::$app->getElements()->deleteElement($review);
        }

        $this->setMessage(Craft::t('reviews', 'Reviews deleted.'));

        return true;
    }
}
