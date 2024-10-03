<?php

namespace Autodesk\Core\Test\Auth\OAuth2;

use Autodesk\Auth\OAuth2\AbstractOAuth2;
use Autodesk\Auth\ScopeValidator;
use Autodesk\Auth\TokenFetcher;
use Autodesk\Core\Exception\InvalidScopeException;
use PHPUnit\Framework\TestCase;

class AbstractOAuth2Test extends TestCase
{
    /**
     * @var AbstractOAuth2
     */
    private AbstractOAuth2 $auth;

    /**
     * @var TokenFetcher
     */
    private TokenFetcher $tokenFetcher;

    /**
     * @var ScopeValidator
     */
    private ScopeValidator $scopeValidator;

    /**
     * Setup before running each test case
     */
    protected function setUp(): void
    {
        $this->tokenFetcher = $this->getMockBuilder(TokenFetcher::class)
            ->disableOriginalConstructor()
            ->addMethods([])
            ->getMock();

        $this->scopeValidator = $this->getMockBuilder(ScopeValidator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isScopeValid', 'isScopeInvalid'])
            ->getMock();

        $arguments = [$this->tokenFetcher, $this->scopeValidator];
        $this->auth = $this->getMockForAbstractClass(AbstractOAuth2::class, $arguments);
    }

    public function test_set_scopes(): void
    {
        $this->scopeValidator
            ->method('isScopeInvalid')
            ->willReturn(false);

        $this->auth->setScopes(['a', 'b']);
        $this->assertEquals(['a', 'b'], $this->auth->getScopes());

        $this->auth->setScopes(['a']);
        $this->assertEquals(['a'], $this->auth->getScopes());
    }

    public function test_add_scope(): void
    {
        $this->scopeValidator
            ->method('isScopeInvalid')
            ->willReturn(false);

        $this->auth->addScope('a');

        $this->assertEquals(['a'], $this->auth->getScopes());

        $this->auth->addScope('b');

        $this->assertEquals(['a', 'b'], $this->auth->getScopes());
    }

    public function test_add_scopes(): void
    {
        $this->scopeValidator
            ->method('isScopeInvalid')
            ->willReturn(false);

        $this->auth->addScopes(['a', 'b']);

        $this->assertEquals(['a', 'b'], $this->auth->getScopes());

        $this->auth->addScopes(['cc', 'c']);

        $this->assertEquals(['a', 'b', 'cc', 'c'], $this->auth->getScopes());
    }

    public function test_add_scope_ignores_existing_scope(): void
    {
        $this->scopeValidator
            ->method('isScopeInvalid')
            ->willReturn(false);

        $this->auth->addScope('a');
        $this->auth->addScope('a');

        $this->assertEquals(['a'], $this->auth->getScopes());

        $this->auth->addScope('b');
        $this->auth->addScope('b');

        $this->assertEquals(['a', 'b'], $this->auth->getScopes());
    }

    public function test_wrong_scope_throws_exception(): void
    {
        $this->scopeValidator
            ->method('isScopeInvalid')
            ->willReturn(true);

        $this->expectException(InvalidScopeException::class);

        $this->auth->addScope('a');
    }

    public function test_has_token(): void
    {
        $this->assertFalse($this->auth->hasAccessToken());

        $this->auth->setAccessToken('XXXX');

        $this->assertTrue($this->auth->hasAccessToken());
        $this->assertEquals('XXXX', $this->auth->getAccessToken());
    }
}