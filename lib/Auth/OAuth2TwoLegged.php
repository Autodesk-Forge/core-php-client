<?php

namespace Autodesk\Core\Auth;

use Autodesk\Core\Exception\RuntimeException;

class OAuth2TwoLegged extends AbstractOAuth2
{
    /**
     * Returns application token
     * @throws RuntimeException
     */
    public function fetchToken()
    {
        parent::fetchAccessToken('authentication/v1/authenticate', 'client_credentials');
    }
}