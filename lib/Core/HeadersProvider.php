<?php

namespace Autodesk\Core;

class HeadersProvider
{
    /**
     * @var string
     */
    private string $version;

    /**
     * @var string
     */
    private string $timestamp;

    /**
     * @var UserAgentGenerator
     */
    private UserAgentGenerator $userAgentGenerator;

    /**
     * HeaderProvider constructor.
     * @param string $version
     * @param UserAgentGenerator|null $userAgentGenerator
     */
    public function __construct(string $version, UserAgentGenerator $userAgentGenerator = null)
    {
        $this->version = $version;
        $this->userAgentGenerator = $userAgentGenerator ?? new UserAgentGenerator($version);
        $this->timestamp = $this->getCurrentTimestamp();
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'x-ads-sdk'          => "php-core-sdk-{$this->version}",
            'x-ads-request-time' => $this->timestamp,
            'User-Agent'         => $this->userAgentGenerator->generate(),
        ];
    }

    /**
     * @return string
     */
    private function getCurrentTimestamp(): string
    {
        $currentTime = time();
        $formattedTimestamp = gmdate('Y-m-d\TH:i:s', $currentTime);
        $milliseconds = substr($currentTime, -3);

        return "{$formattedTimestamp}.{$milliseconds}Z";
    }
}