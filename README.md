# Sanction.

Not just another access control package for [Laravel 4](http://laravel.com),

- Declare a list of permissions in a config file.
- Declare a list of roles, the permissions they can access, and any other roles they inherit from as a config file.

An example roles array:

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
