<?php

namespace Autodesk\Core\Auth;

use Autodesk\Core\Configuration;
use Autodesk\Core\Exception\LogicException;
use Autodesk\Core\Exception\RuntimeException;
use Autodesk\Core\VersionDetector;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class TokenFetcher
{
    const USER_AGENT_PATTERN = 'AutodeskForge/{version}/php';

    const HEADERS = [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ];

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var GuzzleClient
     */
    private $httpClient;

    /**
     * @var VersionDetector
     */
    private $versionDetector;

    /**
     * TokenFetcher constructor.
     * @param Configuration|null $configuration
     * @param GuzzleClient|null $httpClient
     * @param VersionDetector|null $versionDetector
     */
    public function __construct(
        Configuration $configuration = null,
        GuzzleClient $httpClient = null,
        VersionDetector $versionDetector = null
    ) {
        // @codeCoverageIgnoreStart
        if ($configuration === null) {
            $configuration = Configuration::getDefaultConfiguration();
        }

        if ($httpClient === null) {
            $httpClient = new GuzzleClient();
        }

        if ($versionDetector === null) {
            $versionDetector = new VersionDetector();
        }
        // @codeCoverageIgnoreEnd

        $this->configuration = $configuration;
        $this->httpClient = $httpClient;
        $this->versionDetector = $versionDetector;
    }

    /**
     * @param $url
     * @param $grantType
     * @param array $scopes
     * @param array $additionalParams
     * @return array
     * @throws LogicException
     * @throws RuntimeException
     */
    public function fetch($url, $grantType, array $scopes, array $additionalParams = [])
    {
        $this->validateScopesNotEmpty($scopes);

        $formParams = array_merge([
            'client_id'     => $this->configuration->getClientId(),
            'client_secret' => $this->configuration->getClientSecret(),
            'grant_type'    => $grantType,
            'scope'         => implode(' ', $scopes),
        ], $additionalParams);

        $url = "{$this->configuration->getHost()}/{$url}";

        $response = $this->makeRequest($url, $formParams);

        return json_decode($response, true);
    }

    /**
     * @param array $scopes
     * @throws LogicException
     */
    public function validateScopesNotEmpty(array $scopes)
    {
        if (count($scopes) === 0) {
            throw new LogicException('Cannot fetch token when no scopes where defined');
        }
    }

    /**
     * @param $url
     * @param $body
     * @return string
     * @throws RuntimeException
     */
    private function makeRequest($url, $body)
    {
        try {
            $response = $this->httpClient->post($url, [
                'headers'     => $this->getHeaders(),
                'form_params' => $body,
            ]);
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);

            throw new RuntimeException($response['developerMessage']);
        }

        return (string)$response->getBody();
    }

    /**
     * @return array
     */
    private function getHeaders()
    {
        return array_merge(self::HEADERS, [
            'User-Agent' => $this->getUserAgent(),
        ]);
    }

    /**
     * @return mixed
     */
    private function getUserAgent()
    {
        return str_replace('{version}', $this->versionDetector->detect(), self::USER_AGENT_PATTERN);
    }
}