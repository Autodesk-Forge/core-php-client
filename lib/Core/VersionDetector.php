<?php

namespace Autodesk\Core;

class VersionDetector
{
    /**
     * @var string|null
     */
    private string|null $version = null;

    /**
     * @var ComposerJsonFetcher
     */
    private ComposerJsonFetcher $composerJsonFetcher;

    /**
     * VersionDetector constructor.
     * @param ComposerJsonFetcher|null $composerJsonFetcher
     */
    public function __construct(ComposerJsonFetcher $composerJsonFetcher = null)
    {
        $this->composerJsonFetcher = $composerJsonFetcher ?? new ComposerJsonFetcher();
    }

    /**
     * @return mixed
     */
    public function detect(): string
    {
        $this->version = $this->version ?? $this->findVersion();

        return $this->version;
    }

    /**
     * @return string
     */
    private function findVersion(): string
    {
        $data = $this->composerJsonFetcher->fetch();

        if (array_key_exists('version', $data) && $data['version'] !== null) {
            return $data['version'];
        }

        return '0.0';
    }
}