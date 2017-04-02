<?php

namespace Autodesk\Auth\OAuth2;

use Autodesk\Auth\ScopeValidator;
use Autodesk\Auth\TokenFetcher;
use Autodesk\Auth\Configuration;
use Autodesk\Core\Exception\RuntimeException;

class ThreeLeggedAuth extends AbstractOAuth2
{
    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * OAuth2ThreeLegged constructor.
     * @param Configuration|null $configuration
     * @param TokenFetcher|null $tokenFetcher
     * @param ScopeValidator|null $scopeValidator
     */
    public function __construct(
        Configuration $configuration = null,
        TokenFetcher $tokenFetcher = null,
        ScopeValidator $scopeValidator = null
    ) {
        // @codeCoverageIgnoreStart
        if ($configuration === null) {
            $configuration = Configuration::getDefaultConfiguration();
        }
        // @codeCoverageIgnoreEnd

        $this->configuration = $configuration;

        parent::__construct($tokenFetcher, $scopeValidator);
    }

    /**
     * @return string
     */
    public function createAuthUrl()
    {
        $host = $this->configuration->getHost();
        $parameters = http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->configuration->getClientId(),
            'redirect_uri'  => $this->configuration->getRedirectUrl(),
            'scope'         => implode(' ', $this->getScopes()),
        ]);

        return "{$host}/authentication/v1/authorize?{$parameters}";
    }

    /**
     * Returns application token
     * @param $authorizationCode
     * @throws RuntimeException
     */
    public function fetchToken($authorizationCode)
    {
        $additionalParams = [
            'code' => $authorizationCode,
            'redirect_uri' => $this->configuration->getRedirectUrl(),
        ];

        $response = parent::fetchAccessToken('authentication/v1/gettoken', 'authorization_code', $additionalParams);

        $this->saveRefreshToken($response);
    }

    /**
     * @param $refreshToken
     * @throws RuntimeException
     */
    public function refreshToken($refreshToken)
    {
        $additionalParams = [
            'refresh_token' => $refreshToken,
        ];

        $response = parent::fetchAccessToken('authentication/v1/refreshtoken', 'refresh_token', $additionalParams);

        $this->saveRefreshToken($response);
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param $response
     * @throws RuntimeException
     */
    private function saveRefreshToken($response)
    {
        if ( ! array_key_exists('refresh_token', $response)) {
            throw new RuntimeException('Refresh token was not found in the response');
        }

        $this->refreshToken = $response['refresh_token'];
    }
}