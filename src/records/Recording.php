<?php
/**
 * Reactions Work plugin for Craft CMS 3.x
 *
 * Add Facebook style likes, angry and other reactions to your site
 *
 * @link      https://en.24hoursmedia.com
 * @copyright Copyright (c) 2020 info@24hoursmedia.com
 */

namespace twentyfourhoursmedia\reactionswork\records;

use twentyfourhoursmedia\reactionswork\ReactionsWork;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    info@24hoursmedia.com
 * @package   ReactionsWork
 * @since     1.0.0
 */
class Recording extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @access public
     */
    const TABLE_NAME = '{{%reactionswork_recording}}';

    /**
     * Normalizes an int array into a komma separated array
     */
    public function normalizeIntArray($array) {
        $array = $array ?? [];
        $normalized = array_filter($array);
        $normalized = array_map('intval', $normalized);
        return implode(',', $normalized);
    }

    /**
     * Normalizes an int array into a komma separated array
     */
    public function denormalizeIntArray($str) {
        $denormalized = explode(',', $str);
        $denormalized = array_filter($denormalized);
        $denormalized = array_map('intval', $denormalized);
        return $denormalized;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return self::TABLE_NAME;
    }
}
