<?php
/**
 * Created by PhpStorm
 * User: eapbachman
 * Date: 14/03/2020
 */

namespace twentyfourhoursmedia\reactionswork\services;


use craft\base\Component;
use craft\base\Element;
use craft\helpers\UrlHelper;
use yii\web\User;
use twentyfourhoursmedia\reactionswork\helpers\SiteIdHelper;
use twentyfourhoursmedia\reactionswork\ReactionsWork;
use twentyfourhoursmedia\reactionswork\models\Recording;

/**
 * Class ReactionsWorkFacade
 *
 * This facade exposed the public api that is most stable.
 * Use this over the other services.
 *
 * @package twentyfourhoursmedia\reactionswork\services
 */
class ReactionsWorkFacade extends Component
{

    protected function service() : ReactionsWorkService {
        static $service = null;
        return $service ?? $service = ReactionsWork::$plugin->reactionsWorkService;
    }

    /**
     * @example twig: {{ craft.reactions_work.sayHello }}
     * @api
     * @return string
     */
    public function sayHello(): string
    {
        return 'Hello from Reactions Work by 24hoursmedia.com';
    }

    /**
     * Returns all supported reaction handles
     *
     * @return string[]
     */
    public function getReactionHandles(): array
    {
        return $this->service()->getSupportedReactions();
    }

    /**
     * Checks if a user is allowed to react on a specific element
     *
     * @param $user
     * @param Element $element
     * @return bool
     */
    public function isReactable($user, Element $element): bool
    {
        // allow only logged in users
        $userId = $user ? $user->id : \Craft::$app->getUser()->id;
        return $userId !== null;
    }

    /**
     * Submits a reaction
     * @example {% do craft.reactions_work.react('like', entry) %}
     * @example ReactionsWork::$plugin->facade->react('like', $entry)
     *
     * @param string $reactionHandle
     * @param Element $element
     * @param User|null $user
     * @return Recording
     * @throws \craft\errors\SiteNotFoundException
     */
    public function react(string $reactionHandle, Element $element, User $user = null) : Recording
    {
        // find out site and user
        $siteId = $element->siteId ?? \Craft::$app->sites->getCurrentSite()->id;
        $userId = $user ? $user->id : \Craft::$app->getUser()->id;
        if (!$userId) {
            return $this->service()->getAdapter()->getRecording($element->id, $siteId);
        }
        return $this->service()->getAdapter()->register($reactionHandle, $element->id, $siteId, $userId, true);
    }

    /**
     * Submits a reaction
     * @example {% do craft.reactions_work.react('like', entry) %}
     * @example ReactionsWork::$plugin->facade->react('like', $entry)
     *
     * @param string $reactionHandle
     * @param Element $element
     * @param User|null $user
     * @return Recording
     * @throws \craft\errors\SiteNotFoundException
     */
    public function unreact(string $reactionHandle, Element $element, User $user = null) : Recording
    {
        // find out site and user
        $siteId = $element->siteId ?? \Craft::$app->sites->getCurrentSite()->id;
        $userId = $user ? $user->id : \Craft::$app->getUser()->id;
        if (!$userId) {
            return $this->service()->getAdapter()->getRecording($element->id, $siteId);
        }
        return $this->service()->getAdapter()->register($reactionHandle, $element->id, $siteId, $userId, false);
    }

    /**
     * @param string $reactionHandle
     * @param Element $element
     * @param null $user
     * @return Recording
     * @throws \craft\errors\SiteNotFoundException
     */
    public function toggle(string $reactionHandle, Element $element, $user = null) : Recording {
        // find out site and user
        $siteId = $element->siteId ?? \Craft::$app->sites->getCurrentSite()->id;
        $userId = $user ? $user->id : \Craft::$app->getUser()->id;
        if (!$userId) {
            return $this->service()->getAdapter()->getRecording($element->id, $siteId);
        }
        return $this->service()->getAdapter()->toggle($reactionHandle, $element->id, $siteId, $userId);
    }

    /**
     * Returns reactions for an element
     *
     * @param Element $element
     * @return Recording
     * @throws \craft\errors\SiteNotFoundException
     */
    public function getReactions(Element $element) : Recording
    {
        $siteId = $element->siteId ?? \Craft::$app->sites->getCurrentSite()->id;
        return $this->service()->getAdapter()->getRecording($element->id, $siteId);
    }

    /**
     * Returns users for a list of user ids in the same order
     * @param array $userIds
     */
    public function getUsersFromIds(array $userIds)
    {
        $indexedUsers = \craft\elements\User::find()->id($userIds)->indexBy('id')->all();
        return array_filter(array_map(static function($id) use ($indexedUsers) {
            return $indexedUsers[$id] ?? null;
        }, $userIds));
    }

    /**
     * Creates a form url with a form to post reactions to.
     *
     * @param Element $element
     * @param null $user
     * @return string|null
     * @throws \craft\errors\SiteNotFoundException
     */
    public function getFormUrl(Element $element, $user = null) {
        $userId = $user ? $user->id ?? null : null;
        if (!$userId) {
            return null;
        }
        $params = [
            'elementId' => $element->id,
            'siteId' => SiteIdHelper::determineSiteId($element),
            'userId' => $userId
        ];
        $query = ReactionsWork::$plugin->urlSigner->createSignedQuery($params);
        return UrlHelper::actionUrl('reactions-work/reactions/react', $query);
    }

    /**
     * Creates a form url with a form to post reactions to.
     * For ajax, returns json responses.
     *
     * @param Element $element
     * @param null $user
     * @return string|null
     * @throws \craft\errors\SiteNotFoundException
     */
    public function getAjaxFormUrl(Element $element, $user = null) {
        $userId = $user ? $user->id ?? null : null;
        if (!$userId) {
            return null;
        }
        $params = [
            'elementId' => $element->id,
            'siteId' => SiteIdHelper::determineSiteId($element),
            'userId' => $userId
        ];
        $query = ReactionsWork::$plugin->urlSigner->createSignedQuery($params);
        return UrlHelper::actionUrl('reactions-work/reactions/react-xhr', $query);
    }

    public function signUrl($path, array $params = []): string
    {
        $query = ReactionsWork::$plugin->urlSigner->createSignedQuery($params);
        return UrlHelper::url($path, $query);
    }

    public function extractSignedParams(array $queryParams): array
    {
        return ReactionsWork::$plugin->urlSigner->verifySignedParams($queryParams);
    }



}