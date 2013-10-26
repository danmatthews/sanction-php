<?php namespace Curlymoustache\Sanction\Cache;

interface SanctionCacheProviderInterface {
    public function cacheExists();
    public function put($value);
    public function get();
    public function delete();
}
