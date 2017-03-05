<?php

namespace Autodesk\Core;

use Autodesk\Core\Exception\LogicException;
use Autodesk\Core\Exception\WrongEnvironmentException;

class Configuration
{
    /**
     * Environments list
     */
    const ENVIRONMENT_HOSTS = [
        'dev'  => 'https://developer-dev.api.autodesk.com',
        'stg'  => 'https://developer-stg.api.autodesk.com',
        'prod' => 'https://developer.api.autodesk.com',
    ];

    /**
     * @var Configuration|null
     */
    private static $defaultConfiguration = null;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var string
     */
    protected $environment = 'prod';

    /**
     * Gets the default configuration instance
     *
     * @return Configuration
     */
    public static function getDefaultConfiguration()
    {
        if (self::$defaultConfiguration === null) {
            self::$defaultConfiguration = new Configuration();
        }

        return self::$defaultConfiguration;
    }

    /**
     * Sets the detault configuration instance
     *
     * @param Configuration $config An instance of the Configuration Object
     *
     * @return void
     */
    public static function setDefaultConfiguration(Configuration $config)
    {
        self::$defaultConfiguration = $config;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return Configuration
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return Configuration
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     * @return Configuration
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     * @return Configuration
     * @throws LogicException
     */
    public function setEnvironment($environment)
    {
        if ( ! array_key_exists($environment, self::ENVIRONMENT_HOSTS)) {
            throw new WrongEnvironmentException($environment);
        }

        $this->environment = $environment;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return self::ENVIRONMENT_HOSTS[$this->environment];
    }
}