<?php
/**
 * Created by PhpStorm
 * User: eapbachman
 * Date: 17/03/2020
 */

namespace twentyfourhoursmedia\reactionswork\helpers\traits;


use twentyfourhoursmedia\reactionswork\adapters\ReactionsAdapterInterface;
use twentyfourhoursmedia\reactionswork\ReactionsWork;
use twentyfourhoursmedia\reactionswork\services\ReactionsWorkService;
use twentyfourhoursmedia\reactionswork\services\UrlSigner;

trait ServiceProviderTrait
{

    /**
     * @return ReactionsWorkService
     */
    protected function service() : ReactionsWorkService {
        static $service;
        return $service ?? $service = ReactionsWork::$plugin->reactionsWorkService;
    }

    /**
     * @return UrlSigner
     */
    protected function urlSigner() : UrlSigner {
        static $service;
        return $service ?? $service = ReactionsWork::$plugin->urlSigner;
    }

    /**
     * @return ReactionsAdapterInterface
     */
    protected function adapter() : ReactionsAdapterInterface {
        return $this->service()->getAdapter();
    }

}