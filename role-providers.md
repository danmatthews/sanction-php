---
title: Role lookup providers
layout: default
---

If you wish to change how Sanction looks for roles against users, you will need to implement a `RoleLookupProvider`, which you can do by creating a class that implements `Curlymoustache\Sanction\RoleLookup\SanctionRoleLookupProviderInterface`.

```php
<?php namespace Curlymoustache\Sanction\RoleLookup;

interface SanctionRoleLookupProviderInterface {
    public function getRolesForUserId($user_id);
    public function getUsersForRoleId($role_id);
}
```

You can then set this as a new provider by calling `$sanction->setRoleLookupProvider(new MyCustomLookupProvider);`
