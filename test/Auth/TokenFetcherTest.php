<?php

namespace Autodesk\Core\Test\Auth;

use Autodesk\Auth\TokenFetcher;
use Autodesk\Auth\Configuration;
use Autodesk\Core\Exception\LogicException;
use Autodesk\Core\Exception\RuntimeException;
use Autodesk\Core\HeadersProvider;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class TokenFetcherTest extends TestCase
{
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
     * @var TokenFetcher
     */
    private TokenFetcher $tokenFetcher;

    /**
     * @var HeadersProvider
     */
    private HeadersProvider $headersProvider;

    protected function setUp(): void
    {
        $this->configuration = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHost'])
            ->getMock();

        $this->httpClient = $this->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['post'])
            ->getMock();

        $this->headersProvider = $this->getMockBuilder(HeadersProvider::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHeaders'])
            ->getMock();

        $this->headersProvider
            ->method('getHeaders')
            ->willReturn(self::HEADERS);

        $this->tokenFetcher = new TokenFetcher(
            $this->configuration,
            $this->httpClient,
            $this->headersProvider
        );
    }

    public function test_exception_is_thrown_when_no_scopes_defined(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot fetch token when no scopes where defined');

        $this->tokenFetcher->fetch('url', 'grantType', []);
    }

    public function test_call_to_http_client(): void
    {
        $path = 'somepage.php';
        $domain = 'www.test.com/';
        $url = "{$domain}/{$path}";

        $grantType = 'grantType';
        $scopes = ['scopeOne'];
        $additionalParams = [
            'additionalParameter' => 'additionalValue',
        ];

        $options = [
            'headers'     => self::HEADERS,
            'form_params' => [
                'client_id'           => $this->configuration->getClientId(),
                'client_secret'       => $this->configuration->getClientSecret(),
                'grant_type'          => $grantType,
                'scope'               => implode(' ', $scopes),
                'additionalParameter' => 'additionalValue',
            ],
        ];

        $response = ['X-Foo' => 'Bar'];
        $responseObject = new Response(200, [], json_encode($response));

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with($url, $options)
            ->willReturn($responseObject);

        $this->configuration
            ->expects($this->once())
            ->method('getHost')
            ->willReturn($domain);

        $result = $this->tokenFetcher->fetch($path, $grantType, $scopes, $additionalParams);
        $this->assertEquals($response, $result);
    }

    public function test_error_response_handling(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->willReturnCallback(function () {
                $clientException = new ClientException(
                    'Some error with response code of 400',
                    new Request('A', 'B'),
                    new Response(400, [], '{"developerMessage": "Error in the content of the request"}')
                );

                throw $clientException;
            });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error in the content of the request');

        $this->tokenFetcher->fetch('somepage.php', 'grantType', ['a'], []);
    }
}