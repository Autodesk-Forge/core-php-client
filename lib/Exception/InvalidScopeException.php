<?php

namespace Autodesk\Core\Exception;

class InvalidScopeException extends LogicException
{
    /**
     * InvalidScopeException constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct("Cannot add invalid scope '{$name}'");
    }
}