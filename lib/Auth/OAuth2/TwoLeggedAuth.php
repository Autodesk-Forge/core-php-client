<?php

namespace Autodesk\Auth\OAuth2;

use Autodesk\Core\Exception\RuntimeException;

class TwoLeggedAuth extends AbstractOAuth2
{
    /**
     * Returns application token
     * @throws RuntimeException
     */
    public function fetchToken()
    {
        parent::fetchAccessToken('authentication/v2/token', 'client_credentials');
    }
}