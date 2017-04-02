<?php

namespace Autodesk;

use Autodesk\Auth\ScopeValidator;
use PHPUnit\Framework\TestCase;

class ScopeValidatorTest extends TestCase
{
    const SCOPES = ['a', 'b'];
    const VALID_SCOPE = 'a';
    const INVALID_SCOPE = 'c';

    /**
     * @var ScopeValidator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new ScopeValidator(self::SCOPES);
    }

    public function test_valid_scopes()
    {
        $this->assertTrue($this->validator->isScopeValid(self::VALID_SCOPE));
        $this->assertFalse($this->validator->isScopeInvalid(self::VALID_SCOPE));
    }

    public function test_invalid_scopes()
    {
        $this->assertFalse($this->validator->isScopeValid(self::INVALID_SCOPE));
        $this->assertTrue($this->validator->isScopeInvalid(self::INVALID_SCOPE));
    }
}