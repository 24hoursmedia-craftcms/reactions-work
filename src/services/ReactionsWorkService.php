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
        'all'
    ];

    // Public Methods
    // =========================================================================

    /**
     * @return string[]
     */
    public function getSupportedReactions(): array
    {
        return self::REACTION_HANDLES;
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
