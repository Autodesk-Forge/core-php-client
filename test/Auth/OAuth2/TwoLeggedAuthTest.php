<?php

namespace Autodesk;

use Autodesk\Auth\OAuth2\TwoLeggedAuth;
use Autodesk\Auth\ScopeValidator;
use Autodesk\Auth\TokenFetcher;
use Autodesk\Core\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class TwoLeggedAuthTest extends TestCase
{
    /**
     * @var TokenFetcher|PHPUnit_Framework_MockObject_MockObject
     */
    private $tokenFetcher;

    /**
     * @var ScopeValidator|PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeValidator;

    /**
     * @var TwoLeggedAuth
     */
    private $auth;

    /**
     * Setup before running each test case
     */
    public function setUp()
    {
        $this->tokenFetcher = $this->getMockBuilder(TokenFetcher::class)
            ->disableOriginalConstructor()
            ->setMethods(['fetch'])
            ->getMock();

        $this->scopeValidator = $this->getMockBuilder(ScopeValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->auth = new TwoLeggedAuth($this->tokenFetcher, $this->scopeValidator);
    }

    public function test_fetch_token()
    {
        $accessToken = 'XXXX';
        $expiry = 100;

        $this->tokenFetcher
            ->expects($this->once())
            ->method('fetch')
            ->with('authentication/v1/authenticate', 'client_credentials', [])
            ->willReturn(['access_token' => $accessToken, 'expires_in' => $expiry]);

        $this->auth->fetchToken();

        $this->assertEquals($accessToken, $this->auth->getAccessToken());
        $this->assertEquals($expiry, $this->auth->getExpiresIn());
    }

    public function test_exception_is_thrown_when_access_token_is_not_returned_from_fetcher()
    {
        $this->tokenFetcher
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(['expires_in' => 1000]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Access token was not found in the response');

        $this->auth->fetchToken();
    }

    public function test_exception_is_thrown_when_expiry_is_not_returned_from_fetcher()
    {
        $this->tokenFetcher
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(['access_token' => 'XXXX']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expiry was not found in the response');

        $this->auth->fetchToken();
    }
}