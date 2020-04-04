<?php
/**
 * Reactions Work plugin for Craft CMS 3.x
 *
 * Add Facebook style likes, angry and other reactions to your site
 *
 * @link      https://en.24hoursmedia.com
 * @copyright Copyright (c) 2020 info@24hoursmedia.com
 */

namespace twentyfourhoursmedia\reactionswork;

use twentyfourhoursmedia\reactionswork\services\ReactionsWorkFacade;
use twentyfourhoursmedia\reactionswork\services\ReactionsWorkService as ReactionsWorkServiceService;
use twentyfourhoursmedia\reactionswork\services\UrlSigner;
use twentyfourhoursmedia\reactionswork\variables\ReactionsWorkVariable;
use twentyfourhoursmedia\reactionswork\models\Settings;
use twentyfourhoursmedia\reactionswork\fields\ReactionsWorkField as ReactionsWorkFieldField;
use twentyfourhoursmedia\reactionswork\widgets\ReactionsWorkWidget as ReactionsWorkWidgetWidget;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class ReactionsWork
 *
 * @author    info@24hoursmedia.com
 * @package   ReactionsWork
 * @since     1.0.0
 *
 * @property  ReactionsWorkServiceService $reactionsWorkService
 * @property  ReactionsWorkFacade $reactionsWork
 * @property UrlSigner $urlSigner
 */
class ReactionsWork extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var ReactionsWork
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.1.0';

    /**
     * @var bool
     */
    public $hasCpSection = false;

    /**
     * @var bool
     */
    public $hasCpSettings = true;

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
            'reactionsWork' => ReactionsWorkFacade::class,
            'urlSigner' => UrlSigner::class
        ]);

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'twentyfourhoursmedia\reactionswork\console\controllers';
        }

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'reactions-work/default';
            }
        );

        /*
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'reactions-work/default/do-something';
            }
        );
        */

        /*
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ReactionsWorkFieldField::class;
            }
        );
        */

        /*
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ReactionsWorkWidgetWidget::class;
            }
        );
        */

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('reactions_work', ReactionsWorkVariable::class);
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
                'reactions-work',
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
            'reactions-work/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }



}
