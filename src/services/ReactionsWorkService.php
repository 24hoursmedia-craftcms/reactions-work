<?php
/**
 * Reactions Work plugin for Craft CMS 3.x
 *
 * Add Facebook style likes, angry and other reactions to your site
 *
 * @link      https://en.24hoursmedia.com
 * @copyright Copyright (c) 2020 info@24hoursmedia.com
 */

namespace twentyfourhoursmedia\reactionswork\services;

use twentyfourhoursmedia\reactionswork\adapters\DatabaseReactionsAdapter;
use twentyfourhoursmedia\reactionswork\adapters\ReactionsAdapterInterface;
use twentyfourhoursmedia\reactionswork\models\Recording;
use twentyfourhoursmedia\reactionswork\models\Settings;
use twentyfourhoursmedia\reactionswork\models\traits\ReactionsTrait;
use twentyfourhoursmedia\reactionswork\ReactionsWork;

use Craft;
use craft\base\Component;
use twentyfourhoursmedia\reactionswork\records\Recording as RecordingRecord;
/**
 * @author    info@24hoursmedia.com
 * @package   ReactionsWork
 * @since     1.0.0
 */
class ReactionsWorkService extends Component
{

    /**
     * Keep migrations and model in sync
     * @deprecated
     * @see ReactionsTrait
     */
    const REACTION_HANDLES = [
        'like',
        'love',
        'haha',
        'wow',
        'sad',
        'angry'
    ];

    /**
     * Keep migrations and model in sync
     * @see ReactionsTrait
     */
    const ALL_REACTION_HANDLES = [
        'like',
        'love',
        'haha',
        'wow',
        'sad',
        'angry',
        'all',
        'custom1',
        'custom2',
        'custom3',
        'custom4',
        'custom5'
    ];

    // Public Methods
    // =========================================================================

    /**
     * Returns a map of storage handle => custom handle names for enabled handles
     * @return array = ['custom1' => 'ridiculous', 'custom2' => 'monetize']
     */
    protected function getEnabledCustomHandles() {
        $handles = [];
        $settings = ReactionsWork::$plugin->getSettings();
        $attrs  = $settings->getAttributes();
        for ($idx = 1; $idx <= Settings::NUM_CUSTOM_HANDLES; $idx++) {
            if ((bool)$attrs["customReaction{$idx}Enabled"]) {
                $handle = (string)$attrs["customReaction{$idx}Handle"];
                '' !== $handle && $handles["custom{$idx}"] = $handle;
            }
        }
        return $handles;
    }

    /**
     * @return string[] = ['wow' => 'wow', 'like' => 'like', 'custom1' => 'ridicule', 'custom3' => 'monetize']
     */
    public function getSupportedReactions(): array
    {
        static $map = null;
        if ($map !== null) {
            return $map;
        }
        $handles = self::REACTION_HANDLES;
        $map = [];
        foreach ($handles as $handle) {
            $map[$handle] = $handle;
        }
        $map = array_merge($map, $this->getEnabledCustomHandles());
        return $map;
    }

    /**
     * Returns the real handle of a handle or alias
     * @param $handleOrAlias
     * @return false|int|mixed|string
     */
    public function realHandle($handleOrAlias) {
        $map = $this->getSupportedReactions();
        return isset($map[$handleOrAlias]) ? $handleOrAlias : array_search($handleOrAlias, $map, true);
    }

    /**
     * Returns the aliassed handle of a handle or alias
     * @param $handleOrAlias
     * @return false|int|mixed|string
     */
    public function handleAlias($handleOrAlias) {
        $handle = $this->realHandle($handleOrAlias);
        $map = $this->getSupportedReactions();
        return $map[$handle] ?? null;
    }

    /**
     * Get the adapter to store and retrieve reactions that the application is configurerd to use.
     *
     * @return ReactionsAdapterInterface
     */
    public function getAdapter() : ReactionsAdapterInterface {
        static $adapter;
        return $adapter ?? $adapter = new DatabaseReactionsAdapter();
    }

}
