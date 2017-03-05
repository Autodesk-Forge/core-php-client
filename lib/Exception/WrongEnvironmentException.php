<?php

namespace Autodesk\Core\Exception;

class WrongEnvironmentException extends LogicException
{
    /**
     * WrongEnvironmentException constructor.
     * @param string $environment
     */
    public function __construct($environment)
    {
        parent::__construct("Environment with the name of '{$environment}' was not found");
    }
}