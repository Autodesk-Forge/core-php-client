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
    private TokenFetcher $tokenFetcher;

    /**
     * @var ScopeValidator
     */
    private ScopeValidator $scopeValidator;

    /**
     * @var string|null
     */
    private string|null $token = null;

    /**
     * @var int
     */
    private int $expiresIn;

    /**
     * @var array
     */
    private array $scopes = [];

    /**
     * @param TokenFetcher|null $tokenFetcher
     * @param ScopeValidator|null $scopeValidator
     */
    public function __construct(TokenFetcher $tokenFetcher = null, ScopeValidator $scopeValidator = null)
    {
        $this->tokenFetcher = $tokenFetcher ?? new TokenFetcher;
        $this->scopeValidator = $scopeValidator ?? new ScopeValidator;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setAccessToken(string|null $token): void
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * @return bool
     */
    public function hasAccessToken(): bool
    {
        return $this->token !== null;
    }

    /**
     * @param $name
     * @throws LogicException
     */
    public function addScope($name): void
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
    public function addScopes(array $scopes): void
    {
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }

    /**
     * @param array $scopes
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = [];
        $this->addScopes($scopes);
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @param string $url
     * @param string $grantType
     * @param array $additionalParams
     * @return array
     * @throws RuntimeException
     */
    protected function fetchAccessToken(string $url, string $grantType, array $additionalParams = []): array
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
     * @param string $name
     * @return bool
     */
    private function isScopeAlreadyExists(string $name): bool
    {
        return in_array($name, $this->scopes, true);
    }
}