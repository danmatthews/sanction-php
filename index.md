---
layout: default
title: Sanction - Overview &amp; Features
---

### Permission inheritance
You don't need to redeclare your permissions for each user, just tell them to inherit from a user with base permissions.

### Doesn't *require* a database.
Can be used with an `array` driver to build user access control lists.

### Caching of permissions lists.
...to speed up page load times. *This is totally optional*, and can also be swapped-out for custom implementations.

### Swappable integrations.
Thanks to lovely the wonder of PHP `interface`, you can swap out the implementations of the `Cache` and `RoleLookup` parts of your application, meaning that this could potentially be used with any framework or CMS.

## Basic Usage
Let's say we have a user management app, with two roles defined, *standard_user*, and *admin*. Our *standard_user* can create and update users, but our *administrator_user* can also delete users, as well as creating and updating them.

```php
<?php # rules.config.php

return [
    'standard_user' => [
        'permissions' => [
            'create_users',
            'update_users',
        ],
    ],
    'admin' => [
        'permissions' => [
            'delete_users',
        ],
        'inherits_from' => ['standard_user']
    ],
];
```
Apply these rules to an instance of Sanction:

```php
<?php
use Curlymoustache\Sanction\Sanction;
use Curlymoustache\Sanction\RoleLookup\SanctionArrayLookupProvider;

$rules = include 'rules.config.php';

// Build a really simple array of users
$users = [
    [
        'uid' => 10,
        'name' => 'Jim Kirk',
        'roles' => ['admin']
    ],
    [
        'uid' => 32,
        'name' => 'Bones',
        'roles' => ['standard_user']
    ],
];


$sanction = new Sanction(
    $rules,
    null,
    new SanctionArrayLookupProvider( // A simple array lookup class.
        $users,
        'uid' // Use `uid` as the unique user_id
    )
);

// Now we can test these permissions to see if our rules hold true:

// Bones should be able to create users;
$sanction->userHasPermission(32, 'create_users'); // TRUE

// But not delete them:
$sanction->userHasPermission(32, 'delete_users'); // FALSE

// Where as Jim can delete them:
$sanction->userHasPermission(10, 'delete_users'); // TRUE

```



## Clearing the cache.

Or if you want to do it in your code somewhere, call:

```php
$sanction->getCacheProvider()->delete();
```

**More coming soon, stay tuned**
