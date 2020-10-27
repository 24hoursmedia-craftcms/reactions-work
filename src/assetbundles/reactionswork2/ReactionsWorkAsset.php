<?php
/**
 * Reactions Work plugin for Craft CMS 3.x
 *
 * Add Facebook style likes, angry and other reactions to your site
 *
 * @link      https://en.24hoursmedia.com
 * @copyright Copyright (c) 2020 info@24hoursmedia.com
 */

namespace twentyfourhoursmedia\reactionswork\assetbundles\ReactionsWork;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    info@24hoursmedia.com
 * @package   ReactionsWork
 * @since     1.0.0
 */
class ReactionsWorkAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@twentyfourhoursmedia/reactionswork/assetbundles/reactionswork/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/ReactionsWork.js',
        ];

        $this->css = [
            'css/ReactionsWork.css',
        ];

        parent::init();
    }
}
