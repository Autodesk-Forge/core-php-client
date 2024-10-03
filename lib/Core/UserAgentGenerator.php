<?php

namespace Autodesk\Core;

class UserAgentGenerator
{
    /**
     * UserAgentGenerator constructor.
     * @param string $sdkVersion
     */
    public function __construct(private string $sdkVersion)
    {
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $osName = $this->getOsName();
        $osVersion = $this->getOsVersion();
        $engineNameAndVersion = $this->getEngineNameAndVersion();
        $phpVersion = $this->getPhpVersion();

        return "php/{$this->sdkVersion} ({$osName}; {$osVersion}) {$engineNameAndVersion}/{$phpVersion}";
    }

    /**
     * @return string
     */
    private function getPhpVersion(): string
    {
        return PHP_VERSION;
    }

    /**
     * @return string
     */
    private function getOsName(): string
    {
        return php_uname('a');
    }

    /**
     * @return string
     */
    private function getOsVersion(): string
    {
        return php_uname('r');
    }

    /**
     * @return string
     */
    private function getEngineNameAndVersion(): string
    {
        if (array_key_exists('SERVER_SOFTWARE', $_SERVER)
            && (preg_match('/(.*)\/(.*)/', $_SERVER['SERVER_SOFTWARE']) === 1)
        ) {
            // @codeCoverageIgnoreStart
            return $_SERVER['SERVER_SOFTWARE'];
            // @codeCoverageIgnoreEnd
        }

        return 'X/X';
    }
}