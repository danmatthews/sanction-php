<?php namespace Curlymoustache\Sanction;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class SanctionServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('curlymoustache/sanction');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['sanction.cleanup'] = $this->app->share(function() {
			return new \Curlymoustache\Sanction\Commands\SanctionCommand;
		});
		$this->commands('sanction.cleanup');
		$this->app->singleton('sanction', function()
		{

			$cacheProvider      = Config::get('sanction::cache_provider');
			$roleLookupProvider = Config::get('sanction::role_lookup_provider');
			$sanction = new Sanction(
				Config::get('sanction::roles'),
				$cacheProvider ? new $cacheProvider : null,
				$roleLookupProvider ? new $roleLookupProvider : null
			);
			return $sanction;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('sanction');
	}

}
