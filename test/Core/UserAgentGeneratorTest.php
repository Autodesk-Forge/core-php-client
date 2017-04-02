<?php

namespace Autodesk;

use Autodesk\Core\UserAgentGenerator;
use PHPUnit\Framework\TestCase;

class UserAgentGeneratorTest extends TestCase
{
    CONST SDK_VERSION = '0.0.1';
    CONST USER_AGENT_PATTERN = '/php\/0.0.1 \((.*); (\d+)(\.\d+)*\) (.*)\/(\d+)(\.\d+)*/';

    /**
     * @var UserAgentGenerator
     */
    protected $generator;

    public function setUp()
    {
        $this->generator = new UserAgentGenerator(self::SDK_VERSION);
    }

    public function test_user_agent_exists()
    {
        $this->assertNotEmpty($this->generator->generate());
    }

    public function test_user_agent_format()
    {
        $this->assertRegExp(self::USER_AGENT_PATTERN, $this->generator->generate());
    }
}