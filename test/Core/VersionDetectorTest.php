<?php

namespace Autodesk\Core\Test\Core;

use Autodesk\Core\ComposerJsonFetcher;
use Autodesk\Core\VersionDetector;
use PHPUnit\Framework\TestCase;

class VersionDetectorTest extends TestCase
{
    /**
     * @var ComposerJsonFetcher
     */
    private ComposerJsonFetcher $composerJsonFetcher;

    /**
     * @var VersionDetector
     */
    private VersionDetector $versionDetector;

    protected function setUp(): void
    {
        $this->composerJsonFetcher = $this->getMockBuilder(ComposerJsonFetcher::class)
            ->onlyMethods(['fetch'])
            ->getMock();

        $this->versionDetector = new VersionDetector($this->composerJsonFetcher);
    }

    public function test_version_is_detected_correctly(): void
    {
        $this->composerJsonFetcher
            ->method('fetch')
            ->willReturn(['version' => '2.0']);

        $this->assertEquals('2.0', $this->versionDetector->detect());
    }

    public function test_version_is_not_fetched_twice(): void
    {
        $this->composerJsonFetcher
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(['version' => '2.0']);

        $this->versionDetector->detect();
        $this->versionDetector->detect();
        $this->versionDetector->detect();
        $this->versionDetector->detect();
    }

    public function test_fallback_when_version_is_not_in_composer_data(): void
    {
        $this->composerJsonFetcher
            ->method('fetch')
            ->willReturn(['name' => 'somePackageName']);

        $this->assertEquals('0.0', $this->versionDetector->detect());
    }
}