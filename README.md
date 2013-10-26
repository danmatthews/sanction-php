# Sanction.

Not just another access control package for [Laravel 4](http://laravel.com),

- Declare a list of permissions in a config file.
- Declare a list of roles, the permissions they can access, and any other roles they inherit from as a config file.

Example `permissions.php` config file:

```php
<?php
return array(
    'user.create' => 'Create a user',
    'user.invite' => 'Invite a user',
    'user.delete' => 'Delete a user',
);
```

Then an example roles file:

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

- All your permissions are lowercased when they are imported from the config file.
