<?php
/**
 * Created by PhpStorm
 * User: eapbachman
 * Date: 14/03/2020
 */

namespace twentyfourhoursmedia\reactionswork\models;
use twentyfourhoursmedia\reactionswork\helpers\traits\AttributeNamesGeneratingTrait;
use twentyfourhoursmedia\reactionswork\models\traits\ReactionsTrait;
use twentyfourhoursmedia\reactionswork\ReactionsWork;
use twentyfourhoursmedia\reactionswork\records\Recording as RecordingRecord;

use craft\base\Model;
use twentyfourhoursmedia\reactionswork\services\ReactionsWorkService;
use yii\web\User;

class Recording extends Model
{

    use AttributeNamesGeneratingTrait;
    use ReactionsTrait;

    /**
     * @var int
     */
    public $siteId;

    /**
     * @var int
     */
    public $elementId;


    /**
     * @param string $reactionHandle
     * @param int $userId
     * @param bool $set
     * @return bool
     */
    public function register($reactionHandle, $userId, bool $set = true) : bool {
        $reactionHandle = ReactionsWork::$plugin->reactionsWorkService->realHandle($reactionHandle);
        $attrs = [];
        foreach (ReactionsWorkService::ALL_REACTION_HANDLES as $handle) {
            $countAttr = $this->createCountAttrName($handle);
            $usersAttr = $this->createUserIdsAttrName($handle);
            $allCountAttr = $this->createCountAttrName('all');
            $allUsersAttr = $this->createUserIdsAttrName('all');

            $users = $this->attributes[$usersAttr];
            $allUsers = $this->attributes[$allUsersAttr];

            if ($set && $handle === $reactionHandle) {
                // add the user
                array_unshift($users, $userId);
                $users = array_values(array_unique($users));
                if (!in_array($userId, $allUsers, true)) {
                    array_unshift($allUsers, $userId);
                    $allUsers = array_values(array_unique($allUsers));
                }
            } else {
                // remove the user if any
                $users = array_diff($users, [$userId]);
                array_unshift($allUsers, $userId);
                $allUsers = array_values(array_unique($allUsers));
            }
            $attrs[$usersAttr] = $users;
            $attrs[$allUsersAttr] = $allUsers;
            $attrs[$countAttr] = count($users);
            $attrs[$allCountAttr] = count($allUsers);
        }
        $this->setAttributes($attrs, false);
        return true;
    }

    /**
     * Returns the reaction handle for a user or none if null
     * @param int | null $userId
     * @param bool $returnAlias     set to true/false to return the alias or the real internal handle
     * @return string | null
     */
    public function getReaction($userId, bool $returnAlias = true) {
        if (!$userId) {
            return null;
        }
        foreach (ReactionsWorkService::ALL_REACTION_HANDLES as $handle) {
            $usersAttr = $this->createUserIdsAttrName($handle);
            $userIds = $this->attributes[$usersAttr] ?? [];
            if (in_array($userId, $userIds, true)) {
                return $returnAlias ? ReactionsWork::$plugin->reactionsWorkService->handleAlias($handle) : $handle;
            }
        }
        return null;
    }

    public function toggle($reactionHandle, $userId) : bool {
        $selectedHandle = $this->getReaction($userId, false);
        $realHandle = ReactionsWork::$plugin->reactionsWorkService->realHandle($reactionHandle);
        return $this->register($reactionHandle, $userId, $selectedHandle !== $realHandle);
    }

    /**
     * @param $handle
     * @return int|null
     */
    public function countReactions($handle) : int
    {
        $realHandle = ReactionsWork::$plugin->reactionsWorkService->realHandle($handle);
        $v = $this->attributes[$this->createCountAttrName($realHandle)] ?? null;
        return $v !== null ? (int)$v : 0;
    }

    /**
     * @param $handle
     * @return int
     */
    public function countAllReactions() : int
    {
        $total = 0;
        foreach (ReactionsWorkService::ALL_REACTION_HANDLES as $handle) {
            $handle !== 'all' && $total+= $this->countReactions($handle);
        }
        return $total;
    }

    /**
     * @param $handle
     * @param User $user
     * @return bool
     */
    public function canRegister($handle, $user = null): bool
    {
        if (!$user) {
            return false;
        }
        $userId = $user->getId();
        if (!$userId) {
            return false;
        }
        $selectedHandle = $this->getReaction($userId, false);
        $realHandle = ReactionsWork::$plugin->reactionsWorkService->realHandle($handle);
        return $selectedHandle !== $realHandle;
    }

    /**
     * @param $handle
     * @param User $user
     * @return bool
     * @throws \Throwable
     */
    public function canDeregister($handle, $user = null): bool
    {
        if (!$user) {
            return false;
        }
        $userId = $user->getId();
        if (!$userId) {
            return false;
        }
        $selectedHandle = $this->getReaction($userId, false);
        $realHandle = ReactionsWork::$plugin->reactionsWorkService->realHandle($handle);
        return $selectedHandle === $realHandle;
    }

}