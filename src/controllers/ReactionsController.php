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
     * Endpoint to post a regular form to
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionReact()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $adapter = $this->adapter();
        $signer = $this->urlSigner();

        $params = $signer->verifySignedParams($request->getQueryParams());

        $reaction = $request->getBodyParam('reaction');
        $elementId = (int)$params['elementId'];
        $siteId = (int)$params['siteId'];
        $userId = (int)$params['userId'];

        $reaction = $adapter->toggle($reaction, $elementId, $siteId, $userId);

        return $this->redirectToPostedUrl();
    }

    /**
     * Json endpoint
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionReactXhr()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $adapter = $this->adapter();
        $signer = $this->urlSigner();

        $params = $signer->verifySignedParams($request->getQueryParams());

        $reaction = $request->getBodyParam('reaction');
        $elementId = (int)$params['elementId'];
        $siteId = (int)$params['siteId'];
        $userId = (int)$params['userId'];

        $recording = $adapter->toggle($reaction, $elementId, $siteId, $userId);
        $dto = [
            'success' => $recording ? true : false,
            'message' => null
        ];
        return $this->asJson($dto);
    }


}
