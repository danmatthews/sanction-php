# Sanction.

**Note: Requires PHP5.4+**

Yep, another access control package for PHP, including some neat integration with [Laravel 4](http://laravel.com).

Declare a list of roles, the permissions they can access, and any other roles they inherit from as a config array (or file, or whatever you like!), then let `Sanction` do the rest.

Attaching roles to users is your job, this merely sets what they *can* do once they're attached - But we provide some helpers for Laravel (and hopefully some more generic stuff soon too).

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

## Features

- Permission inheritance, you don't need to redeclare your permissions for each user, just tell them to inherit from a user with base permissions.
- Doesn't *require* a database, can be used with an `array` driver to build user access control lists.
- Caching of permissions lists to speed up page load times. *This is totally optional*, and can also be swapped-out for custom implementations.

### Swappable integrations.

Thanks to lovely the wonder of PHP `interface`, you can swap out the implementations of the `Cache` and `RoleLookup` parts of your application, meaning that this could potentially be used with any framework or CMS.

#### Cache Providers

To adjust Sanction to work with other frameworks or even just plain ol' PHP, you can create your own CacheProvider by implementing `Curlymoustache\Sanction\Cache\SanctionCacheProviderInterface` and ensuring it returns the right stuff.

You can then set this as a new provider by calling `$sanction->setCacheProvider(new MyCustomCacheProvider);`

#### Role Lookup Providers

If you wish to change how Sanction looks for roles against users, you will need to implement a `RoleLookupProvider`, which you can do by creating a class that implements `Curlymoustache\Sanction\RoleLookup\SanctionRoleLookupProviderInterface`.

You can then set this as a new provider by calling `$sanction->setRoleLookupProvider(new MyCustomLookupProvider);`

## Installation

Installation is done via [Composer](http://getcomposer.org), simply add this line to your `composer.json` file (PS - versioned release coming soon!):

```
require: {
    "curlymoustache/sanction" : "dev-master"
}
```

Then run `$ composer update` or `$ composer install` on the command line (if you have composer installed).

### Don't have composer installed?

You can download a copy of composer to your working directory by typing:

```
$ curl -S http://getcomposer.org/installer | php
```

Then you can run composer commands by using:

```
$ php composer.phar <command>
```

If you want to make the copy of composer 'global', then you can move the file to somewhere in your `$PATH`, like `/usr/local/bin`:

```
$ sudo mv composer.phar /usr/local/bin/composer
```

### Getting Started Using Laravel 4

Sanction and Laravel 4 are the best of friends! Add the following to your `app/config/app.php` file, at the end of the `$providers` array:

```php
'Curlymoustache\Sanction\SanctionServiceProvider'
```

Then add the following alias to the `$aliases` array:

```php
'Sanction' => 'Curlymoustache\Sanction\SanctionFacade',
```

This will make calls to `Sanction::methodName()` possible.

#### Publish the config files

*Now **publish the config files**, this is needed*:

```
php artisan config:publish curlymoustache/sanction
```

You should now be able to open `app/config/packages/curlymoustache/sanction/roles.php` and `app/config/packages/curlymoustache/sanction/config.php`.

There! Now you can add your role definitions into the `roles.php` config file.

#### Run the migration

Run the migration from the package, this will install a `roles` table into your database, allowing you to persist role information for users.

Simply run:

```
$ php artisan migrate --package="curlymoustache/sanction"
```

#### Eloquent Extension

Are you using the default `Eloquent` user model? Good news then! There's a PHP Trait that you can include into your `User` model to enable some shortcuts.

Just `use` the trait in your User model:

```php
<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    use Curlymoustache\Sanction\Extensions\EloquentExtension;

...
```

This will allow to use the following methods:

<table>
<tr><th>Method</th><th>Description</th>
<tr>
    <td>
    <code>User::usersWithRole($role_id)</code></td><td>Return an eloquent collection of users with a particular role.</td>
</tr>


<tr><td><code>$user->addRole($role_id)</code></td><td>Add a role to the current instance of `User`</td></tr>

<tr><td><code>$user->deleteRole($role_id)</code></td><td>Delete a role from the current instance of `User`.</td></tr>

<tr><td><code>$user->hasPermission($permission_name)</code></td><td>Verify a user has a permission.</td></tr>

<tr><td><code>$user->hasPermissions(array $permissions)</code></td><td>An array of permissions names, will only return true if a user has <strong>all</strong> of these.</td></tr>

<tr><td><code>$user->getRoles()</code></td><td>Returns the list of roles associated with the user, if any.</td></tr>

<tr><td><code>$user->getPermissions()</code></td><td>Returns the list of permissions associated with the user, if any.</td></tr>

</table>

>Remember that to use this extension, your model must **directly** extend the `Eloquent` class, and be using `id` as the primary key.

#### Clear the permissions cache with Artisan

When you update `app/config/packages/curlymoustache/sanction/roles.php` with caching enabled in the config file, you will need to clear the permissions cache for your new rules to take effect.

Sanction provides a handy artisan command to do so, this will call the `delete` method on whatever `cache_provider` you have setup in config.php.


```
$ php artisan sanction:cleanup
```

**More coming soon, stay tuned**
