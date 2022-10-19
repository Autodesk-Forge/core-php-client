<?php

namespace Autodesk\Auth;

class ScopeValidator
{
    private const SCOPES = [
        'data:read',
        'data:write',
        'data:create',
        'data:search',
        'bucket:create',
        'bucket:read',
        'bucket:update',
        'bucket:delete',
        'code:all',
        'account:read',
        'account:write',
        'user-profile:read',
        'viewables:read',
    ];

    /**
     * @var array
     */
    private array $scopes;

    /**
     * ScopeValidator constructor.
     * @param array|null $scopes
     */
    public function __construct(array $scopes = null)
    {
        $this->scopes = $scopes ?? self::SCOPES;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isScopeValid(string $name): bool
    {
        return in_array($name, $this->scopes, true);
    }

    /**
     * @param $name
     * @return bool
     */
    public function isScopeInvalid(string $name): bool
    {
        return ! $this->isScopeValid($name);
    }
}
