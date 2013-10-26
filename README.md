# Sanction.

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

### Using Laravel 4?

Sanction and Laravel 4 are the best of friends! Add the following to your `app/config/app.php` file, at the end of the `$providers` array:

```php
'Curlymoustache\Sanction\SanctionServiceProvider'
```

Then add the following alias to the `$aliases` array:

```php
'Sanction' => 'Curlymoustache\Sanction\SanctionFacade',
```

This will make calls to `Sanction::methodName()` possible.

#### Artisan commands

Sanction provides a handy artisan command to clear the cache, if you're using the default `LaravelSanctionCacheProvider`:

```
$ php artisan sanction:clean
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

## Installation

**More coming soon, stay tuned**
