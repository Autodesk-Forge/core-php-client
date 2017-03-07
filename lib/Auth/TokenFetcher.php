<?php

namespace Autodesk\Core\Auth;

use Autodesk\Core\Configuration;
use Autodesk\Core\Exception\LogicException;
use Autodesk\Core\Exception\RuntimeException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class TokenFetcher
{
    const HEADERS = [
        'Content-Type' => 'application/x-www-form-urlencoded',
        'x-sdk-type'   => 'PHP',
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
     * @var array
     */
    private $packageConfiguration;

    /**
     * @var null
     */
    private $forceTimestamp;

    /**
     * TokenFetcher constructor.
     * @param Configuration|null $configuration
     * @param GuzzleClient|null $httpClient
     * @param array $packageConfiguration
     * @param null $forceTimestamp
     */
    public function __construct(
        Configuration $configuration = null,
        GuzzleClient $httpClient = null,
        array $packageConfiguration = null,
        $forceTimestamp = null
    ) {
        // @codeCoverageIgnoreStart
        if ($configuration === null) {
            $configuration = Configuration::getDefaultConfiguration();
        }

        if ($httpClient === null) {
            $httpClient = new GuzzleClient();
        }

        if ($packageConfiguration === null) {
            $packageConfiguration = require(dirname(__FILE__) . '/../../config.php');
        }
        // @codeCoverageIgnoreEnd

        $this->configuration = $configuration;
        $this->httpClient = $httpClient;
        $this->packageConfiguration = $packageConfiguration;
        $this->forceTimestamp = $forceTimestamp;
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
            'x-sdk-usage'          => $this->packageConfiguration['usage'],
            'x-sdk-version'        => $this->packageConfiguration['version'],
            'x-sdk-sent-timestamp' => ($this->forceTimestamp !== null) ? $this->forceTimestamp : time(),
        ]);
    }
}