<?php namespace Curlymoustache\Sanction\Cache;

use Illuminate\Support\Facades\Cache;

class LaravelSanctionCacheProvider implements SanctionCacheProviderInterface {

    protected $cacheKey = 'sanction_acl';

    public function cacheExists()
    {
        return Cache::has($this->cacheKey);
    }

    public function put($data)
    {
        Cache::forever($this->cacheKey, $data);
    }

    public function get()
    {
        return Cache::get($this->cacheKey);
    }

    public function delete()
    {
        Cache::forget($this->cacheKey);
    }

    public function setCacheKey($key) {

        if (is_string($key)) {

            $this->cacheKey = $key;

        } else {

            throw new \Exception("CacheKeyWasNotAStringException");

        }
    }
}
