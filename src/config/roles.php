<?php

/*
|--------------------------------------------------------------------------
| Setting up roles...
|--------------------------------------------------------------------------
|
| Each array key should be a role slug, using underscores instead of spaces,
| you should try and keep these simple and lowercase.
|
| !!! IF YOU HAVE SANCTION'S CACHING ENABLED REMEMBER TO RUN:
| php artisan sanction:cleanup
| TO CLEAR THE CACHE AFTER EACH TIME YOU EDIT THIS FILE !!!
|
| The value of each item should be an array with the following keys:
|
| $permissions - A one dimensional array of permission names as strings.
| $inherits_from - This should be a list of valid role names to
|  inherit permissions from.
|
| The dot syntax (users.create) inside the permissions arrays has no
| special significance - it's simply how i choose to write my default
| rules, you could seperate names with underscores or anything, really, like:
|
| 'create_users', 'update_users', 'user|create', 'user:update'
|
*/
return array(
    'super_administrator' => array(
        'permissions' => array(
            'users.create',
            'users.update',
            'users.delete',
        ),
        'inherits_from' => array('standard_user', 'administrator'),
    ),
    'standard_user' => array(
        'permissions' => array(
            'pages.add',
            'pages.update',
            'self.update',
        ),
    ),
    'administrator' => array(
        'permissions' => array(
            'users.create',
            'users.update'
        ),
        'inherits_from' => array('standard_user'),
    ),
);
