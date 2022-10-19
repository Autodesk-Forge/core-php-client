<?php

namespace Autodesk\Core\Exception;

class InvalidScopeException extends LogicException
{
    /**
     * InvalidScopeException constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct("Cannot add invalid scope '{$name}'");
    }
}