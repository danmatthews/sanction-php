# Sanction.

**Note: Requires PHP5.4+**

Yep, another access control package for PHP, including some neat integration with [Laravel 4](http://laravel.com).

Declare a list of roles, the permissions they can access, and any other roles they inherit from as a config array (or file, or whatever you like!), then let `Sanction` do the rest.

Attaching roles to users is your job, this merely sets what they *can* do once they're attached - But we provide some helpers for Laravel (and hopefully some more generic stuff soon too).

## Features

- Permission inheritance, you don't need to redeclare your permissions for each user, just tell them to inherit from a user with base permissions.
- Doesn't *require* a database, can be used with an `array` driver to build user access control lists.
- Caching of permissions lists to speed up page load times. *This is totally optional*, and can also be swapped-out for custom implementations.

### Swappable integrations.

Thanks to lovely the wonder of PHP `interface`, you can swap out the implementations of the `Cache` and `RoleLookup` parts of your application, meaning that this could potentially be used with any framework or CMS.

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
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    use Curlymoustache\Sanction\Extensions\EloquentExtension;

...
```

This will allow to use the following methods:

<table>
<tr>
    <td>
    <code>usersWithRole($role_id)</code></td><td>Return an eloquent collection of users with a particular role.</td>
</tr>

</table>

`$user->addRole($role_id)` - Add a role to the current instance of `User`

`$user->deleteRole($role_id)` - Delete a role from the current instance of `User`.

`$user->hasPermission($permission_name)` - Verify a user has a permission.

`$user->hasPermissions(array $permissions)` - An array of permissions names, will only return true if a user has **all** of these.

#### Clear the permissions cache with Artisan

Sanction provides a handy artisan command to clear the cache, if you're using the default `LaravelSanctionCacheProvider`:


```
$ php artisan sanction:cleanup
```

## Example rules

An example roles array (declared in a config file):

```php
<?php
return array(
    'standard_user' => array(
        'display_name' => 'Standard User',
        'permissions' => array(
            'user.invite',
        ),
    ),
    'admin' => array(
        'display_name' => 'Administrator',
        'inherits_from' => array(
            'standard_user', // This allows them to user.invite too!
        ),
        'permissions' => array(
            'user.delete',
            'user.create',
        ),
    ),
);
```

**More coming soon, stay tuned**
