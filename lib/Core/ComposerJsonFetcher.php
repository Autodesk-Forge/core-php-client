<?php

namespace Autodesk\Core;

/**
 * Class ComposerJsonFetcher
 * @package Autodesk\Core
 *
 * @codeCoverageIgnore
 */
class ComposerJsonFetcher
{
    /**
     * @return array
     */
    public function fetch(): array
    {
        $composerJsonFileLocation = __DIR__ . '/../../composer.json';

        return (array) json_decode(file_get_contents($composerJsonFileLocation), true);
    }
}