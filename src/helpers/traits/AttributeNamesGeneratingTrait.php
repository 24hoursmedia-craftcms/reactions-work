<?php
/**
 * Created by PhpStorm
 * User: eapbachman
 * Date: 15/03/2020
 */

namespace twentyfourhoursmedia\reactionswork\helpers\traits;

/**
 * Trait AttributeNamesGeneratingTrait
 *
 * Generates names used in db migrations, records and models.
 * Be extremely cautions when changing the strategy!
 *
 * @package twentyfourhoursmedia\reactionswork\helpers\traits
 */
trait AttributeNamesGeneratingTrait
{

    protected function createCountAttrName($handle): string
    {
        return strtolower($handle) . 'Count';
    }

    protected function createUserIdsAttrName($handle): string
    {
        return strtolower($handle) . 'UserIds';
    }

}