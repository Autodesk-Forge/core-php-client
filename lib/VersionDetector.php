<?php

namespace Autodesk\Core;

class VersionDetector
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var ComposerJsonFetcher
     */
    private $composerJsonFetcher;

    /**
     * VersionDetector constructor.
     * @param ComposerJsonFetcher $composerJsonFetcher
     */
    public function __construct(ComposerJsonFetcher $composerJsonFetcher = null)
    {
        // @codeCoverageIgnoreStart
        if ($composerJsonFetcher === null) {
            $composerJsonFetcher = new ComposerJsonFetcher();
        }
        // @codeCoverageIgnoreEnd

        $this->composerJsonFetcher = $composerJsonFetcher;
    }

    /**
     * @return mixed
     */
    public function detect()
    {
        if ( ! is_null($this->version)) {
            return $this->version;
        }

        $this->version = $this->findVersion();

        return $this->version;
    }

    /**
     * @return mixed
     */
    private function findVersion()
    {
        $data = $this->composerJsonFetcher->fetch();

        if (array_key_exists('version', $data) && $data['version'] !== null) {
            return $data['version'];
        }

        return '0.0';
    }
}