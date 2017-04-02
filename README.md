# Core Component (internal)

## Requirements

PHP 5.4.0 and later

## Installation & Usage
### Composer

To install the bindings via [Composer](http://getcomposer.org/) run:
```
composer require autodesk/core
```

### Manual Installation

Download the files and include `autoload.php`:

```php
require_once('/path/to/AutodeskCore/autoload.php');
```

## Tests

To run the unit tests:

```
composer install
./vendor/bin/phpunit
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

### Two legged

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

Autodesk\Auth\Configuration::getDefaultConfiguration()
    ->setClientId('XXXXXX')
    ->setClientSecret('XXXXXX');

$twoLeggedAuth = new Autodesk\Auth\OAuth2\TwoLeggedAuth();
$twoLeggedAuth->setScopes(['bucket:read']);

/**
 * Other options to manage the scopes
 *
 * $twoLeggedAuth->addScope('data:read');
 * $twoLeggedAuth->addScopes([]);
 * $twoLeggedAuth->setScopes($scopes);
 */

if (isset($cache['applicationToken']) && $cache['expiry'] > time()) {
    $twoLeggedAuth->setAccessToken($cache['applicationToken']);
} else {
    $twoLeggedAuth->fetchToken();

    $cache['applicationToken'] = $twoLeggedAuth->getAccessToken();
    $cache['expiry'] = time() + $twoLeggedAuth->getExpiresIn();
}

```

### Three legged

index.php

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

session_start();

Autodesk\Auth\Configuration::getDefaultConfiguration()
    ->setClientId('XXXXXX')
    ->setClientSecret('XXXXXX')
    ->setRedirectUrl("http://{$_SERVER['HTTP_HOST']}/callback.php");

$threeLeggedAuth = new Autodesk\Auth\OAuth2\ThreeLeggedAuth();
$threeLeggedAuth->addScope('code:all');

if (isset($_SESSION['isAuthenticated']) && $_SESSION['expiry'] > time()) {
    $threeLeggedAuth->setAccessToken($_SESSION['accessToken']);

    print_r('Token was fetched from the session');
} else {
    if (isset($_SESSION['refreshToken'])) {
        $threeLeggedAuth->refreshToken($_SESSION['refreshToken']);

        $_SESSION['isAuthenticated'] = true;
        $_SESSION['accessToken'] = $threeLeggedAuth->getAccessToken();
        $_SESSION['refreshToken'] = $threeLeggedAuth->getRefreshToken();
        $_SESSION['expiry'] = time() + $threeLeggedAuth->getExpiresIn();

        print_r('Token was refreshed');
    } else {
        $redirectTo = $threeLeggedAuth->createAuthUrl();

        header('Location: ' . filter_var($redirectTo, FILTER_SANITIZE_URL));
        return;
    }
}


```

callback.php
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

session_start();

Autodesk\Auth\Configuration::getDefaultConfiguration()
    ->setClientId('XXXXXX')
    ->setClientSecret('XXXXXX')
    ->setRedirectUrl("http://{$_SERVER['HTTP_HOST']}/callback.php");

$threeLeggedAuth = new Autodesk\Auth\OAuth2\ThreeLeggedAuth();
$threeLeggedAuth->addScopes(['data:read']);

if (isset($_GET['code']) && $_GET['code']) {
    $threeLeggedAuth->fetchToken($_GET['code']);

    $_SESSION['isAuthenticated'] = true;
    $_SESSION['accessToken'] = $threeLeggedAuth->getAccessToken();
    $_SESSION['refreshToken'] = $threeLeggedAuth->getRefreshToken();
    $_SESSION['expiry'] = time() + $threeLeggedAuth->getExpiresIn();

    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    header('Location: ' . filter_var($url, FILTER_SANITIZE_URL));
} else {
    header('Location: ' . $threeLeggedAuth->createAuthUrl());
}
```

## Documentation For Authorization

 - **data:read**: The application will be able to read the end user’s data within the Autodesk ecosystem.
 - **data:write**: The application will be able to create, update, and delete data on behalf of the end user within the Autodesk ecosystem.
 - **data:create**: The application will be able to create data on behalf of the end user within the Autodesk ecosystem.
 - **data:search**: The application will be able to search the end user’s data within the Autodesk ecosystem.
 - **bucket:create**: The application will be able to create an OSS bucket it will own.
 - **bucket:read**: The application will be able to read the metadata and list contents for OSS buckets that it has access to.
 - **bucket:update**: The application will be able to set permissions and entitlements for OSS buckets that it has permission to modify.
 - **bucket:delete**: The application will be able to delete a bucket that it has permission to delete.
 - **code:all**: The application will be able to author and execute code on behalf of the end user (e.g., scripts processed by the Design Automation API).
 - **account:read**: For Product APIs, the application will be able to read the account data the end user has entitlements to.
 - **account:write**: For Product APIs, the application will be able to update the account data the end user has entitlements to.
 - **user-profile:read**: The application will be able to read the end user’s profile data.

## Author

forge.help@autodesk.com


