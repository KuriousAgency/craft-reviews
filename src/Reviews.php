<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews;

use kuriousagency\reviews\services\ReviewsService as Service;
use kuriousagency\reviews\variables\ReviewsVariable;
use kuriousagency\reviews\fields\Reviews as ReviewsField;
use kuriousagency\reviews\widgets\ReviewsWidget;
use kuriousagency\reviews\elements\Review;
use kuriousagency\reviews\twigextensions\ReviewsTwigExtension;
use kuriousagency\reviews\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Elements;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class Reviews
 *
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 *
 * @property  ReviewsServiceService $reviewsService
 */
class Reviews extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Reviews
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
	public $schemaVersion = '0.0.2';
	
	public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
		self::$plugin = $this;
		
		$this->setComponents([
			'service' => Service::class,
		]);

		Craft::$app->view->registerTwigExtension(new ReviewsTwigExtension());

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
				$event->rules['reviews'] = 'reviews/default/index';
                $event->rules['reviews/new'] = 'reviews/default/edit';
                $event->rules['reviews/new/<purchasableType:[-\w]+>'] = 'reviews/default/edit';
				$event->rules['reviews/<id:\d+>'] = 'reviews/default/edit';
            }
        );

        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
				$event->types[] = Review::class;
            }
        );

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ReviewsField::class;
            }
        );

        /*Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ReviewsWidget::class;
            }
        );*/

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
				//$variable->set('reviews', ReviewsVariable::class);
				$variable->attachBehavior('reviews', ReviewsVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'reviews',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }


    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'reviews/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

}
