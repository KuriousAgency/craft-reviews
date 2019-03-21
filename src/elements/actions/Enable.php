<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\elements;

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

class Enable extends ElementAction
{
    // Public Properties
    // =========================================================================

    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $message;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('dsd', 'Enable…');
    }

    /**
     * @inheritdoc
     */
    /*public function getTriggerHtml()
    {
        $orderStatuses = Json::encode(Plugin::getInstance()->getOrderStatuses()->getAllOrderStatuses());
        $type = Json::encode(static::class);

        $js = <<<EOT
(function()
{
    var trigger = new Craft.ElementActionTrigger({
        type: {$type},
        batch: true,
        activate: function(\$selectedItems)
        {
            var currentSourceStatusHandle = Craft.elementIndex.sourceKey.split(':')[1];
            var currentOrderStatus = null;
            var orderStatuses = $orderStatuses;
            for (i = 0; i < orderStatuses.length; i++) {
                if(orderStatuses[i].handle == currentSourceStatusHandle){
                    currentOrderStatus = orderStatuses[i];
                }
            }
            var modal = new Craft.Commerce.UpdateOrderStatusModal(currentOrderStatus,orderStatuses, {
                onSubmit: function(data){
                   Craft.elementIndex.submitAction('Commerce_UpdateOrderStatus', data);
                   modal.hide();
                }
            });
        }
    });
})();
EOT;

        Craft::$app->getView()->registerJs($js);

        return null;
    }*/

    /**
     * @inheritdoc
     */
    public function performAction(ElementQueryInterface $query): bool
    {
        $projects = $query->all();

        foreach ($projects as $project) {
			$project->status = null;//Project::STATUS_DISABLED;
			DSD::$plugin->projects->saveProject($project);
            //Craft::$app->getElements()->saveElement($project);
        }

        return true;
    }
}
