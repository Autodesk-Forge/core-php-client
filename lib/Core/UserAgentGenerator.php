<?php

namespace Autodesk\Core;

class UserAgentGenerator
{
    /**
     * @var string
     */
    private $sdkVersion;

    /**
     * UserAgentGenerator constructor.
     * @param $sdkVersion
     */
    public function __construct($sdkVersion)
    {
        $this->sdkVersion = $sdkVersion;
    }
    
    /**
     * @return string
     */
    public function generate()
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
    private function getPhpVersion()
    {
        return phpversion();
    }

    /**
     * @return string
     */
    private function getOsName()
    {
        return php_uname('a');
    }

    /**
     * @return string
     */
    private function getOsVersion()
    {
        return php_uname('r');
    }

    /**
     * @return string
     */
    private function getEngineNameAndVersion()
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