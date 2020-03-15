<?php
/**
 * Created by PhpStorm
 * User: eapbachman
 * Date: 15/03/2020
 */

namespace twentyfourhoursmedia\reactionswork\services;
use craft\base\Component;
use twentyfourhoursmedia\reactionswork\ReactionsWork;

class UrlSigner extends Component
{

    const SIGNATURE_FIELD = 'signature';
    const SIGNED_PARAMS_FIELD = '_signedVal';

    protected function getSigningKey() : string {
        return (string)ReactionsWork::$plugin->settings->signKey;
    }

    /**
     * Creates new query params that cannot be tampered with and can be retrieved with:
     * self::verifySignedParams
     *
     * @param array $queryParams
     * @return array
     */
    public function createSignedQuery(array $queryParams) {
        $signedVal = json_encode($queryParams);
        return [
            self::SIGNATURE_FIELD => sha1($this->getSigningKey() . $signedVal),
            self::SIGNED_PARAMS_FIELD => $signedVal
        ];
    }

    /**
     * Retrieves the original query params from signed query params
     *
     * @param array $queryParams
     * @return array
     */
    public function verifySignedParams(array $queryParams) : array
    {
        $signature = $queryParams[self::SIGNATURE_FIELD];
        $expectedSignature = sha1($this->getSigningKey() . $queryParams[self::SIGNED_PARAMS_FIELD]);
        if ($expectedSignature !== $signature) {
            throw new BadRequestHttpException('Invalid signature');
        }
        return json_decode($queryParams[self::SIGNED_PARAMS_FIELD] ?? '', true);
    }


}
