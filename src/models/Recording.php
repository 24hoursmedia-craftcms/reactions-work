<?php
/**
 * Created by PhpStorm
 * User: eapbachman
 * Date: 14/03/2020
 */

namespace twentyfourhoursmedia\reactionswork\models;
use twentyfourhoursmedia\reactionswork\helpers\traits\AttributeNamesGeneratingTrait;
use twentyfourhoursmedia\reactionswork\models\traits\ReactionsTrait;
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



    public function react($reactionHandle, $userId) : bool {
        $attrs = [];
        foreach (ReactionsWorkService::REACTION_HANDLES as $handle) {
            $countAttr = $this->createCountAttrName($handle);
            $usersAttr = $this->createUserIdsAttrName($handle);
            $allCountAttr = $this->createCountAttrName('all');
            $allUsersAttr = $this->createUserIdsAttrName('all');

            $users = $this->attributes[$usersAttr];
            $allUsers = $this->attributes[$allUsersAttr];

            if ($handle === $reactionHandle) {
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
            }
            $attrs[$usersAttr] = $users;
            $attrs[$allUsersAttr] = $allUsers;
            $attrs[$countAttr] = count($users);
            $attrs[$allCountAttr] = count($allUsers);
        }
        $this->setAttributes($attrs, false);
        return true;
    }

    public function toggle($reactionHandle, $userId) : bool {
        $attrs = [];
        $wasAdded = false;
        foreach (ReactionsWorkService::REACTION_HANDLES as $handle) {
            $countAttr = $this->createCountAttrName($handle);
            $usersAttr = $this->createUserIdsAttrName($handle);
            $allCountAttr = $this->createCountAttrName('all');
            $allUsersAttr = $this->createUserIdsAttrName('all');

            $users = $this->attributes[$usersAttr];
            $allUsers = $this->attributes[$allUsersAttr];

            if ($handle === $reactionHandle) {
                if (in_array($userId, $users, true)) {
                    // remove the user if any
                    $users = array_diff($users, [$userId]);
                    $users = array_values(array_unique($users));
                } else {
                    // add the user
                    $users[] = $userId;
                    $users = array_values(array_unique($users));
                    $wasAdded = true;
                }
            } else {
                // remove the user if any
                $users = array_diff($users, [$userId]);
            }
            $attrs[$usersAttr] = $users;
            $attrs[$countAttr] = count($users);
        }

        if ($wasAdded) {
            if (!in_array($userId, $allUsers, true)) {
                array_unshift($allUsers, $userId);
                $allUsers = array_values(array_unique($allUsers));
            }
        } else {
            $allUsers = array_diff($allUsers, [$userId]);
            $allUsers = array_values(array_unique($allUsers));
        }
        $attrs[$allUsersAttr] = $allUsers;
        $attrs[$allCountAttr] = count($allUsers);

        $this->setAttributes($attrs, false);
        return true;
    }

    /**
     * @param $handle
     * @return int|null
     */
    public function countReactions($handle)
    {
        $v = $this->attributes[$this->createCountAttrName($handle)] ?? null;
        return $v !== null ? (int)$v : null;
    }

    /**
     * @param $handle
     * @return int
     */
    public function countAllReactions() : int
    {
        $total = 0;
        foreach (ReactionsWorkService::REACTION_HANDLES as $handle) {
            $total+= $this->countReactions($handle);
        }
        return $total;
    }

    /**
     * @param $handle
     * @param User $user
     * @return bool
     */
    public function can($handle, $user = null) {
        if (!$user) {
            return false;
        }
        $userId = $user->getId();
        if (!$userId) {
            return false;
        }

        $attrName = $this->createUserIdsAttrName($handle);
        $attrs = $this->getAttributes([$attrName]);
        $userIds = $attrs[$attrName] ?? [];
        return !in_array((int)$userId, $userIds, true);
    }

    /**
     * @param $handle
     * @param User $user
     * @return bool
     * @throws \Throwable
     */
    public function canUnreact($handle, $user = null)
    {
        if (!$user) {
            return false;
        }
        $userId = $user->getId();
        if (!$userId) {
            return false;
        }

        $attrName = $this->createUserIdsAttrName($handle);
        $attrs = $this->getAttributes([$attrName]);
        $userIds = $attrs[$attrName] ?? [];
        return in_array((int)$userId, $userIds, true);
    }

}