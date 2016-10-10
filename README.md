# chubbyphp-security

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-security.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-security)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-security/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-security)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-security/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-security)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-security/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-security/?branch=master)

## Description

A simple security solution.

## Requirements

 * php: ~7.0
 * chubbyphp/chubbyphp-error-handler: ~1.0@dev
 * chubbyphp/chubbyphp-model: ~1.0@dev

## Suggest

 * chubbyphp/chubbyphp-session: ~1.0@dev
 * pimple/pimple: ~3.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-security][1].

## Usage

### Authentication

#### AuthenticationProvider (Pimple)

```{.php}
<?php

use Chubbyphp\Security\Authentication\AuthenticationProvider;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Pimple\Container;

$container->register(new AuthenticationProvider);

$container->extend('security.authentication.authentications', function (array $authentications) use ($container) {
    $authentications[] = new FormAuthentication(...);

    return $authentications;
});

$container['security.authentication']->isAuthenticated($request);
```

#### AuthenticationMiddleware

```{.php}
<?php

use Chubbyphp\Security\Authentication\AuthenticationMiddleware;
use Chubbyphp\Security\Authentication\FormAuthentication;

$middleware = new AuthenticationMiddleware(new FormAuthentication(...));
$middleware($request, $response);
```

#### FormAuthentication

```{.php}
<?php

use Chubbyphp\Security\Authentication\FormAuthentication;
use Chubbyphp\Security\Authentication\PasswordManager;
use Chubbyphp\Session\Session;

$authentication = new FormAuthentication(new PasswordManager, new Session, ...);
$authentication->login($request);
$authentication->logout($request);
$authentication->isAuthenticated($request);
$authentication->getAuthenticatedUser($request);
```

#### PasswordManager

```{.php}
<?php

use Chubbyphp\Security\Authentication\PasswordManager;

$manager = new PasswordManager();
$hash = $manager->hash('password');
$manager->verify('password', $hash);
```

### Authorization

#### AuthorizationProvider (Pimple)

```{.php}
<?php

use Chubbyphp\Security\Authorization\AuthorizationProvider;
use Chubbyphp\Security\Authorization\RoleAuthorization;
use Pimple\Container;

$container->register(new AuthorizationProvider);

$container->extend('security.authorization.rolehierarchy', function (array $rolehierarchy) use ($container) {
    $rolehierarchy['ADMIN'] = ['USER_MANAGEMENT'];
    $rolehierarchy['USER_MANAGEMENT'] = ['USER_LIST', 'USER_CREATE', 'USER_EDIT', 'USER_VIEW', 'USER_DELETE'];

    return $rolehierarchy;
});

$container['security.authorization.rolehierarchyresolver']->resolve($roles);

$container->extend('security.authorization.authorizations', function (array $authorizations) use ($container) {
    $authorizations[] = new RoleAuthorization(...);

    return $$authorizations;
});

$container['security.authorization']->isGranted($user, 'USER_EDIT');
```

#### RoleAuthorization

```{.php}
<?php

use Chubbyphp\Security\Authorization\RoleAuthorization;
use Chubbyphp\Security\Authorization\RoleHierarchyResolver;

$user->setRoles(['ADMIN']);

$resolver = new RoleHierarchyResolver([
    'ADMIN' => ['USER_MANAGEMENT'],
    'USER_MANAGEMENT' => ['USER_CREATE', 'USER_EDIT', 'USER_VIEW', 'USER_DELETE']
]);

$authorization = new RoleAuthorization($resolver);
$authorization->isGranted($user, 'USER_EDIT'); // true
```

#### RoleHierarchyResolver

```{.php}
<?php

use Chubbyphp\Security\Authorization\RoleHierarchyResolver;

$user->setRoles(['ADMIN']);

$resolver = new RoleHierarchyResolver([
    'ADMIN' => ['USER_MANAGEMENT'],
    'USER_MANAGEMENT' => ['USER_CREATE', 'USER_EDIT', 'USER_VIEW', 'USER_DELETE']
]);

$resolver->resolve(['ADMIN']);
```

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-security

## Copyright

Dominik Zogg 2016
