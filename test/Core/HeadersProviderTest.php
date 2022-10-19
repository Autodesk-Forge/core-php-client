<?php

namespace Autodesk\Core\Test\Core;

use Autodesk\Core\HeadersProvider;
use Autodesk\Core\UserAgentGenerator;
use PHPUnit\Framework\TestCase;

class HeadersProviderTest extends TestCase
{
    CONST VERSION = '0.0.1';
    CONST USER_AGENT = 'Just some user agent';

    /**
     * @var HeadersProvider
     */
    private HeadersProvider $provider;

    /**
     * @var UserAgentGenerator
     */
    private UserAgentGenerator $userAgentGenerator;

    protected function setUp(): void
    {
        $this->userAgentGenerator = $this->getMockBuilder(UserAgentGenerator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generate'])
            ->getMock();

        $this->userAgentGenerator
            ->method('generate')
            ->willReturn(self::USER_AGENT);

        $this->provider = new HeadersProvider(self::VERSION, $this->userAgentGenerator);
    }

    public function test_x_ads_sdk_header_exists(): void
    {
        $this->checkHeaderExists('x-ads-sdk');
    }

    public function test_x_ads_sdk_header_is_correct(): void
    {
        $version = self::VERSION;

        $headers = $this->provider->getHeaders();
        $this->assertEquals("php-core-sdk-{$version}" , $headers['x-ads-sdk']);
    }

    public function test_x_ads_request_time_exists(): void
    {
        $this->checkHeaderExists('x-ads-request-time');
    }

    public function test_x_ads_request_time_format_is_correct(): void
    {
        $headers = $this->provider->getHeaders();
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z/', $headers['x-ads-request-time']);
    }

    public function test_user_agent_exists(): void
    {
        $this->checkHeaderExists('User-Agent');
    }

    public function test_user_agent_is_taken_from_the_generator_class(): void
    {
        $headers = $this->provider->getHeaders();
        $this->assertEquals(self::USER_AGENT , $headers['User-Agent']);
    }

    private function checkHeaderExists(string $name): void
    {
        $this->assertArrayHasKey($name, $this->provider->getHeaders());
    }
}