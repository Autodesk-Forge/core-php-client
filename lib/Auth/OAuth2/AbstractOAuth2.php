<?php

namespace Autodesk\Auth\OAuth2;

use Autodesk\Auth\ScopeValidator;
use Autodesk\Auth\TokenFetcher;
use Autodesk\Core\Exception\InvalidScopeException;
use Autodesk\Core\Exception\LogicException;
use Autodesk\Core\Exception\RuntimeException;

abstract class AbstractOAuth2
{
    /**
     * @var TokenFetcher
     */
    private $tokenFetcher;

    /**
     * @var ScopeValidator
     */
    private $scopeValidator;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @var array
     */
    private $scopes = [];

    /**
     * @param TokenFetcher $tokenFetcher
     * @param ScopeValidator $scopeValidator
     */
    public function __construct(TokenFetcher $tokenFetcher = null, ScopeValidator $scopeValidator = null)
    {
        // @codeCoverageIgnoreStart
        if ($tokenFetcher === null) {
            $tokenFetcher = new TokenFetcher();
        }

        if ($scopeValidator === null) {
            $scopeValidator = new ScopeValidator();
        }
        // @codeCoverageIgnoreEnd

        $this->tokenFetcher = $tokenFetcher;
        $this->scopeValidator = $scopeValidator;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->token;
    }

    /**
     * @param $token
     */
    public function setAccessToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @return bool
     */
    public function hasAccessToken()
    {
        return $this->token !== null;
    }

    /**
     * @param $name
     * @throws LogicException
     */
    public function addScope($name)
    {
        if ($this->isScopeAlreadyExists($name)) {
            return;
        }

        if ($this->scopeValidator->isScopeInvalid($name)) {
            throw new InvalidScopeException($name);
        }

        $this->scopes[] = $name;
    }

    /**
     * @param array $scopes
     */
    public function addScopes(array $scopes)
    {
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }

    /**
     * @param array $scopes
     */
    public function setScopes(array $scopes)
    {
        $this->scopes = [];
        $this->addScopes($scopes);
    }

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param $url
     * @param $grantType
     * @param array $additionalParams
     * @return array
     * @throws RuntimeException
     */
    protected function fetchAccessToken($url, $grantType, array $additionalParams = [])
    {
        $response = $this->tokenFetcher->fetch($url, $grantType, $this->scopes, $additionalParams);

        if ( ! array_key_exists('access_token', $response)) {
            throw new RuntimeException('Access token was not found in the response');
        }

        if ( ! array_key_exists('expires_in', $response)) {
            throw new RuntimeException('Expiry was not found in the response');
        }

        $this->token = $response['access_token'];
        $this->expiresIn = $response['expires_in'];

        return $response;
    }

    /**
     * @param $name
     * @return bool
     */
    private function isScopeAlreadyExists($name)
    {
        return in_array($name, $this->scopes);
    }
}