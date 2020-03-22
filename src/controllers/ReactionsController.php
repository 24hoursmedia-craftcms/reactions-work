<?php
/**
 * Reactions Work plugin for Craft CMS 3.x
 *
 * Add Facebook style likes, angry and other reactions to your site
 *
 * @link      https://en.24hoursmedia.com
 * @copyright Copyright (c) 2020 info@24hoursmedia.com
 */

namespace twentyfourhoursmedia\reactionswork\controllers;

use twentyfourhoursmedia\reactionswork\helpers\traits\ServiceProviderTrait;
use twentyfourhoursmedia\reactionswork\ReactionsWork;

use Craft;
use craft\web\Controller;
use twentyfourhoursmedia\reactionswork\services\ReactionsWorkFacade;
use yii\web\BadRequestHttpException;

/**
 * @author    info@24hoursmedia.com
 * @package   ReactionsWork
 * @since     1.0.0
 */
class ReactionsController extends Controller
{

    use ServiceProviderTrait;

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * @return \twentyfourhoursmedia\reactionswork\models\Recording | null
     * @throws BadRequestHttpException
     */
    protected function handleReaction() {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $adapter = $this->adapter();
        $signer = $this->urlSigner();
        $params = $signer->verifySignedParams($request->getQueryParams());
        $reaction = $request->getBodyParam('reaction');
        $actionType = $request->getBodyParam('react','toggle');
        $elementId = (int)$params['elementId'];
        $siteId = (int)$params['siteId'];
        $userId = (int)$params['userId'];

        switch ($actionType) {
            case 'toggle':
                $recording = $adapter->toggle($reaction, $elementId, $siteId, $userId);
                break;
            case 'set':
                $recording = $adapter->register($reaction, $elementId, $siteId, $userId, true);
                break;
            case 'unset':
                $recording = $adapter->register($reaction, $elementId, $siteId, $userId, false);
                break;
            default:
                throw new BadRequestHttpException('Invalid reaction');
        }
        return $recording;
    }

    /**
     * Endpoint to post a regular form to
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionReact()
    {
        $recording = $this->handleReaction();
        return $this->redirectToPostedUrl();
    }

    /**
     * Json endpoint
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionReactXhr()
    {
        $recording = $this->handleReaction();
        $dto = [
            'success' => $recording ? true : false,
            'message' => null
        ];
        return $this->asJson($dto);
    }

}
