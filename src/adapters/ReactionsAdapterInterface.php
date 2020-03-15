<?php
/**
 * Created by PhpStorm
 * User: eapbachman
 * Date: 15/03/2020
 */

namespace twentyfourhoursmedia\reactionswork\adapters;
use twentyfourhoursmedia\reactionswork\models\Recording;

/**
 * Interface ReactionsAdapterInterface
 * These interfaces provide storage mechanisms to retrieve or store a recording from somewhere
 *
 */
interface ReactionsAdapterInterface
{

    /**
     * Retrieves a recording model for an element for a site
     *
     * @param int $elementId
     * @param int $siteId
     * @return Recording
     */
    public function getRecording(int $elementId, int $siteId) : Recording;

    /**
     * Registers a reaction for an element
     *
     * @param string $reactionHandle
     * @param int $elementId
     * @param int $siteId
     * @param int $userId
     * @return Recording
     */
    public function react(string $reactionHandle, int $elementId, int $siteId, int $userId) : Recording;

    /**
     * Toggles a reaction for an element
     *
     * @param string $reactionHandle
     * @param int $elementId
     * @param int $siteId
     * @param int $userId
     * @return Recording
     */
    public function toggle(string $reactionHandle, int $elementId, int $siteId, int $userId) : Recording;

}