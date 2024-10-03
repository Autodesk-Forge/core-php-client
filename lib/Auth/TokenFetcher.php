<?php

namespace Autodesk\Auth;

use Autodesk\Core\Exception\LogicException;
use Autodesk\Core\Exception\RuntimeException;
use Autodesk\Core\HeadersProvider;
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
    private Configuration $configuration;

    /**
     * @var GuzzleClient
     */
    private GuzzleClient $httpClient;

    /**
     * @var HeadersProvider
     */
    private HeadersProvider $headersProvider;

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
        $this->configuration = $configuration ?? Configuration::getDefaultConfiguration();
        $this->httpClient = $httpClient ?? new GuzzleClient();
        $this->headersProvider = $headersProvider ?? new HeadersProvider((new VersionDetector)->detect());
    }

    /**
     * @param string $url
     * @param string $grantType
     * @param array $scopes
     * @param array $additionalParams
     * @return array
     * @throws RuntimeException|LogicException
     */
    public function fetch(string $url, string $grantType, array $scopes, array $additionalParams = []): array
    {
        if(count($scopes) === 0)
        {
            throw new LogicException('Cannot fetch token when no scopes where defined');
        }

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
     * @param string $url
     * @param array $body
     * @return string
     * @throws RuntimeException
     */
    private function makeRequest(string $url, array $body): string
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
