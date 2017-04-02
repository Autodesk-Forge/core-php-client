<?php

namespace Autodesk;

use Autodesk\Auth\OAuth2\AbstractOAuth2;
use Autodesk\Auth\ScopeValidator;
use Autodesk\Auth\TokenFetcher;
use Autodesk\Core\Exception\InvalidScopeException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class AbstractOAuth2Test extends TestCase
{
    /**
     * @var AbstractOAuth2
     */
    private $auth;

    /**
     * @var TokenFetcher|PHPUnit_Framework_MockObject_MockObject
     */
    private $tokenFetcher;

    /**
     * @var ScopeValidator|PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeValidator;

    /**
     * Setup before running each test case
     */
    public function setUp()
    {
        $this->tokenFetcher = $this->getMockBuilder(TokenFetcher::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->scopeValidator = $this->getMockBuilder(ScopeValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['isScopeValid', 'isScopeInvalid'])
            ->getMock();

        $arguments = [$this->tokenFetcher, $this->scopeValidator];
        $this->auth = $this->getMockForAbstractClass(AbstractOAuth2::class, $arguments);
    }

    public function test_set_scopes()
    {
        $this->scopeValidator
            ->method('isScopeInvalid')
            ->willReturn(false);

        $this->auth->setScopes(['a', 'b']);
        $this->assertEquals(['a', 'b'], $this->auth->getScopes());

        $this->auth->setScopes(['a']);
        $this->assertEquals(['a'], $this->auth->getScopes());
    }

    public function test_add_scope()
    {
        $this->scopeValidator
            ->method('isScopeInvalid')
            ->willReturn(false);

        $this->auth->addScope('a');

        $this->assertEquals(['a'], $this->auth->getScopes());

        $this->auth->addScope('b');

        $this->assertEquals(['a', 'b'], $this->auth->getScopes());
    }

    public function test_add_scopes()
    {
        $this->scopeValidator
            ->method('isScopeInvalid')
            ->willReturn(false);

        $this->auth->addScopes(['a', 'b']);

        $this->assertEquals(['a', 'b'], $this->auth->getScopes());

        $this->auth->addScopes(['cc', 'c']);

        $this->assertEquals(['a', 'b', 'cc', 'c'], $this->auth->getScopes());
    }

    public function test_add_scope_ignores_existing_scope()
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

    public function test_wrong_scope_throws_exception()
    {
        $this->scopeValidator
            ->method('isScopeInvalid')
            ->willReturn(true);

        $this->expectException(InvalidScopeException::class);

        $this->auth->addScope('a');
    }

    public function test_has_token()
    {
        $this->assertFalse($this->auth->hasAccessToken());

        $this->auth->setAccessToken('XXXX');

        $this->assertTrue($this->auth->hasAccessToken());
        $this->assertEquals('XXXX', $this->auth->getAccessToken());
    }
}