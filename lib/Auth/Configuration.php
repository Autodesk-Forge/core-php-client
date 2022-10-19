<?php

namespace Autodesk\Auth;

class Configuration
{
    /**
     * @var Configuration|null
     */
    private static Configuration|null $defaultConfiguration = null;

    /**
     * @var string|null
     */
    protected string|null $clientId = null;

    /**
     * @var string|null
     */
    protected string|null $clientSecret = null;

    /**
     * @var string
     */
    protected string $redirectUrl;

    /**
     * @var string
     */
    protected string $host = 'https://developer.api.autodesk.com';

    /**
     * Gets the default configuration instance
     *
     * @return Configuration
     */
    public static function getDefaultConfiguration(): Configuration
    {
        self::$defaultConfiguration = self::$defaultConfiguration ?? new self;

        return self::$defaultConfiguration;
    }

    /**
     * Sets the default configuration instance
     *
     * @param Configuration $config An instance of the Configuration Object
     *
     * @return void
     */
    public static function setDefaultConfiguration(Configuration $config): void
    {
        self::$defaultConfiguration = $config;
    }

    /**
     * @return string|null
     */
    public function getClientId(): string|null
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return Configuration
     */
    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientSecret(): string|null
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return Configuration
     */
    public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     * @return Configuration
     */
    public function setRedirectUrl(string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }
}