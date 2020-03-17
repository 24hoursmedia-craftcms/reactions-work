<?php
/**
 * Reactions Work plugin for Craft CMS 3.x
 *
 * Add Facebook style likes, angry and other reactions to your site
 *
 * @link      https://en.24hoursmedia.com
 * @copyright Copyright (c) 2020 info@24hoursmedia.com
 */

namespace twentyfourhoursmedia\reactionswork\models;

use twentyfourhoursmedia\reactionswork\ReactionsWork;

use Craft;
use craft\base\Model;

/**
 * @author    info@24hoursmedia.com
 * @package   ReactionsWork
 * @since     1.0.0
 */
class Settings extends Model
{
    const NUM_CUSTOM_HANDLES = 5;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $signKey = '';

    // The database schema takes into account 5 custom Reactions,
    // they can be enabled through the settings
    public $customReaction1Enabled = false;
    public $customReaction1Handle = 'custom1';
    public $customReaction2Enabled = false;
    public $customReaction2Handle = 'custom2';
    public $customReaction3Enabled = false;
    public $customReaction3Handle = 'custom3';
    public $customReaction4Enabled = false;
    public $customReaction4Handle = 'custom4';
    public $customReaction5Enabled = false;
    public $customReaction5Handle = 'custom5';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['signKey', 'string'],
            ['signKey', 'default', 'value' => bin2hex(random_bytes(32))],
        ];

        for ($i = 1; $i <= 5; $i++) {
            $rules[] = ["customReaction{$i}Enabled", 'boolean'];
            $rules[] = ["customReaction{$i}Handle", 'string'];
            $rules[] = ["customReaction{$i}Handle", 'default', 'value' => "custom{$i}"];
            $rules[] = ["customReaction{$i}Handle", 'match', 'pattern' => '/^[\w-]+$/', 'message' => 'Only alphanumeric characters and the _ symbol are allowed'];
        }

        return $rules;
    }
}
