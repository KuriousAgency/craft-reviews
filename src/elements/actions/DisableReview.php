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
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Json;

/**
 * Class Disable
 *
 * @property null|string $triggerHtml the action’s trigger HTML
 * @property string $triggerLabel the action’s trigger label
 */

class DisableReview extends ElementAction
{
    // Public Properties
    // =========================================================================


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('reviews', 'Disable');
    }

    /**
     * @inheritdoc
     */
    public function performAction(ElementQueryInterface $query = null): bool
    {
        if (!$query) {
            return false;
        }

		foreach ($query->all() as $review)
		{
			$review->enabled = false;
			Craft::$app->getElements()->saveElement($review);
		}

        return true;
    }
}
