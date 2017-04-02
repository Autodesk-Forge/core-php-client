<?php

namespace Autodesk;

use Autodesk\Core\ComposerJsonFetcher;
use Autodesk\Core\VersionDetector;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class VersionDetectorTest extends TestCase
{
    /**
     * @var ComposerJsonFetcher|PHPUnit_Framework_MockObject_MockObject
     */
    private $composerJsonFetcher;

    /**
     * @var VersionDetector
     */
    private $versionDetector;

    public function setUp()
    {
        $this->composerJsonFetcher = $this->getMockBuilder(ComposerJsonFetcher::class)
            ->setMethods(['fetch'])
            ->getMock();

        $this->versionDetector = new VersionDetector($this->composerJsonFetcher);
    }

    public function test_version_is_detected_correctly()
    {
        $this->composerJsonFetcher
            ->method('fetch')
            ->willReturn(['version' => '2.0']);

        $this->assertEquals('2.0', $this->versionDetector->detect());
    }

    public function test_version_is_not_fetched_twice()
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

    public function test_fallback_when_version_is_not_in_composer_data()
    {
        $this->composerJsonFetcher
            ->method('fetch')
            ->willReturn(['name' => 'somePackageName']);

        $this->assertEquals('0.0', $this->versionDetector->detect());
    }
}