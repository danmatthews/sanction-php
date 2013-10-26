<?php

return array(
    /**
     * Cache the ACL rules once registered?
     * This will cache them indefinitely, and each time you update
     * your permissions config, you will need to run
     * `php artisan permissions:refresh`
     */
    'cache_provider' => 'Curlymoustache\Sanction\Cache\LaravelSanctionCacheProvider',
    'role_lookup_provider' => 'Curlymoustache\Sanction\RoleLookup\LaravelDatabaseLookupProvider',
);
