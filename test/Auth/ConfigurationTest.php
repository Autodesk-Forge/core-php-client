<?php

namespace Autodesk\Core\Test\Auth;

use Autodesk\Auth\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
    }

    public function test_set_default_configuration(): void
    {
        $newConfig = new Configuration();
        $newConfig->setRedirectUrl('someUrl');

        Configuration::setDefaultConfiguration($newConfig);

        $this->assertEquals($newConfig, Configuration::getDefaultConfiguration());
    }

    public function test_client_id_storing(): void
    {
        $clientId = 'ASDFSDUBFSA ODFS';

        $this->configuration->setClientId($clientId);

        $this->assertEquals($clientId, $this->configuration->getClientId());
    }

    public function test_client_secret_storing(): void
    {
        $clientSecret = 'ASDFSDUBFSA ODFS';

        $this->configuration->setClientSecret($clientSecret);

        $this->assertEquals($clientSecret, $this->configuration->getClientSecret());
    }

    public function test_redirect_url_storing(): void
    {
        $redirectUrl = 'www.test.com/callback.php';

        $this->configuration->setRedirectUrl($redirectUrl);

        $this->assertEquals($redirectUrl, $this->configuration->getRedirectUrl());
    }

    public function test_url(): void
    {
        $this->assertEquals('https://developer.api.autodesk.com', $this->configuration->getHost());

        $this->configuration->setHost('http://test.com');
        $this->assertEquals('http://test.com', $this->configuration->getHost());
    }
}