<?php

namespace Autodesk\Core;

class HeadersProvider
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $timestamp;

    /**
     * @var UserAgentGenerator
     */
    private $userAgentGenerator;

    /**
     * HeaderProvider constructor.
     * @param $version
     * @param UserAgentGenerator $userAgentGenerator
     */
    public function __construct($version, UserAgentGenerator $userAgentGenerator = null)
    {
        // @codeCoverageIgnoreStart
        if ($userAgentGenerator === null) {
            $userAgentGenerator = new UserAgentGenerator($version);
        }
        // @codeCoverageIgnoreEnd

        $this->version = $version;
        $this->timestamp = $this->getCurrentTimestamp();
        $this->userAgentGenerator = $userAgentGenerator;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'x-ads-sdk'          => "php-core-sdk-{$this->version}",
            'x-ads-request-time' => $this->timestamp,
            'User-Agent'         => $this->userAgentGenerator->generate(),
        ];
    }

    /**
     * @return false|string
     */
    private function getCurrentTimestamp()
    {
        $currentTime = time();
        $formattedTimestamp = gmdate('Y-m-d\TH:i:s', $currentTime);
        $milliseconds = substr($currentTime, -3);

        return "{$formattedTimestamp}.{$milliseconds}Z";
    }
}