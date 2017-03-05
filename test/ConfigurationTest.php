<?php

namespace Autodesk\Core;

use Autodesk\Core\Exception\WrongEnvironmentException;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration();
    }

    public function test_set_default_configuration()
    {
        $newConfig = new Configuration();
        $newConfig->setRedirectUrl('someUrl');

        Configuration::setDefaultConfiguration($newConfig);

        $this->assertEquals($newConfig, Configuration::getDefaultConfiguration());
    }

    public function test_client_id_storing()
    {
        $clientId = 'ASDFSDUBFSA ODFS';

        $this->configuration->setClientId($clientId);

        $this->assertEquals($clientId, $this->configuration->getClientId());
    }

    public function test_client_secret_storing()
    {
        $clientSecret = 'ASDFSDUBFSA ODFS';

        $this->configuration->setClientSecret($clientSecret);

        $this->assertEquals($clientSecret, $this->configuration->getClientSecret());
    }

    public function test_redirect_url_storing()
    {
        $redirectUrl = 'www.test.com/callback.php';

        $this->configuration->setRedirectUrl($redirectUrl);

        $this->assertEquals($redirectUrl, $this->configuration->getRedirectUrl());
    }

    public function test_that_setting_wrong_environment_throws_exception()
    {
        $this->expectException(WrongEnvironmentException::class);

        $this->configuration->setEnvironment('wrong environment');
    }

    /**
     * @dataProvider provide_environments
     *
     * @param $environment
     * @param $host
     */
    public function test_set_environment($environment, $host)
    {
        $this->configuration->setEnvironment($environment);

        $this->assertEquals($environment, $this->configuration->getEnvironment());
        $this->assertEquals($host, $this->configuration->getHost());
    }

    public function provide_environments()
    {
        return [
            ['dev', 'https://developer-dev.api.autodesk.com'],
            ['stg', 'https://developer-stg.api.autodesk.com'],
            ['prod', 'https://developer.api.autodesk.com'],
        ];
    }
}