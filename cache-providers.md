---
title: Cache providers
layout: default
---

To adjust Sanction to work with other frameworks or even just plain ol' PHP, you can create your own CacheProvider by implementing `Curlymoustache\Sanction\Cache\SanctionCacheProviderInterface` and ensuring it returns the right stuff.

```php
<?php namespace Curlymoustache\Sanction\Cache;

interface SanctionCacheProviderInterface {
    public function cacheExists();
    public function put($value);
    public function get();
    public function delete();
}
```

You can then set this as a new provider by calling `$sanction->setCacheProvider(new MyCustomCacheProvider);`
