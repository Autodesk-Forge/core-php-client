<?php

namespace Autodesk;

use Autodesk\Core\HeadersProvider;
use Autodesk\Core\UserAgentGenerator;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class HeadersProviderTest extends TestCase
{
    CONST VERSION = '0.0.1';
    CONST USER_AGENT = 'Just some user agent';

    /**
     * @var HeadersProvider
     */
    private $provider;

    /**
     * @var UserAgentGenerator|PHPUnit_Framework_MockObject_MockObject
     */
    private $userAgentGenerator;

    public function setUp()
    {
        $this->userAgentGenerator = $this->getMockBuilder(UserAgentGenerator::class)
            ->disableOriginalConstructor()
            ->setMethods(['generate'])
            ->getMock();

        $this->userAgentGenerator
            ->method('generate')
            ->willReturn(self::USER_AGENT);

        $this->provider = new HeadersProvider(self::VERSION, $this->userAgentGenerator);
    }

    public function test_x_ads_sdk_header_exists()
    {
        $this->checkHeaderExists('x-ads-sdk');
    }

    public function test_x_ads_sdk_header_is_correct()
    {
        $version = self::VERSION;

        $headers = $this->provider->getHeaders();
        $this->assertEquals("php-core-sdk-{$version}" , $headers['x-ads-sdk']);
    }

    public function test_x_ads_request_time_exists()
    {
        $this->checkHeaderExists('x-ads-request-time');
    }

    public function test_x_ads_request_time_format_is_correct()
    {
        $headers = $this->provider->getHeaders();
        $this->assertRegExp('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z/', $headers['x-ads-request-time']);
    }

    public function test_user_agent_exists()
    {
        $this->checkHeaderExists('User-Agent');
    }

    public function test_user_agent_is_taken_from_the_generator_class()
    {
        $headers = $this->provider->getHeaders();
        $this->assertEquals(self::USER_AGENT , $headers['User-Agent']);
    }

    private function checkHeaderExists($name)
    {
        $this->assertArrayHasKey($name, $this->provider->getHeaders());
    }
}