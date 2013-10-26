<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Cache provider
    |--------------------------------------------------------------------------
    |
    | When laravel boots up Sanction, it will by default run through all your
    | rules in the `roles.php` config file. Once it has set these up, it will cache the
    | resulting Zend Acl Object instance for faster loads.
    |
    | The default provider uses Laravel's `Cache` methods/facade.
    | If you don't wish to do any caching, set this to `null`.
    |
    */
    'cache_provider' => 'Curlymoustache\Sanction\Cache\LaravelSanctionCacheProvider',

    /*
    |--------------------------------------------------------------------------
    | Role Lookup Provider
    |--------------------------------------------------------------------------
    |
    | In order to stay as flexible as possible, Sanction allows you to customise
    | how it looks for roles against users.
    |
    | The LaravelDatabaseLookupProvider uses the migration provided with this package
    | and assumes that you have `roles` database table with `user_id`(integer)
    | and `role_id` (varchar) columns.
    |
    */
    'role_lookup_provider' => 'Curlymoustache\Sanction\RoleLookup\LaravelDatabaseLookupProvider',
);
