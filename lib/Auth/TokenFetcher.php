<?php

namespace AutodeskForge\Auth;

use AutodeskForge\Core\Exception\LogicException;
use AutodeskForge\Core\Exception\RuntimeException;
use AutodeskForge\Core\HeadersProvider;
use AutodeskForge\Core\VersionDetector;
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
     * @var HeadersProvider
     */
    private $headersProvider;

    /**
     * TokenFetcher constructor.
     * @param Configuration|null $configuration
     * @param GuzzleClient|null $httpClient
     * @param HeadersProvider|null $headersProvider
     */
    public function __construct(
        Configuration $configuration = null,
        GuzzleClient $httpClient = null,
        HeadersProvider $headersProvider = null
    ) {
        // @codeCoverageIgnoreStart
        if ($configuration === null) {
            $configuration = Configuration::getDefaultConfiguration();
        }

        if ($httpClient === null) {
            $httpClient = new GuzzleClient();
        }

        if ($headersProvider === null) {
            // Use the CORE version detector
            $versionDetector = new VersionDetector();

            $headersProvider = new HeadersProvider($versionDetector->detect());
        }
        // @codeCoverageIgnoreEnd

        $this->configuration = $configuration;
        $this->httpClient = $httpClient;
        $this->headersProvider = $headersProvider;
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
                'headers'     => $this->headersProvider->getHeaders(),
                'form_params' => $body,
            ]);
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);

            throw new RuntimeException($response['developerMessage']);
        }

        return (string)$response->getBody();
    }
}