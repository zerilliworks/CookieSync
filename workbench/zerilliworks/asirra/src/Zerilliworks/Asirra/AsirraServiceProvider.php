<?php namespace Zerilliworks\Asirra;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;

class AsirraServiceProvider extends ServiceProvider {

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
		$this->package('zerilliworks/asirra');

        $this->createValidator();
        $this->createFormMacro();
	}

    public function createValidator()
    {
        $validator = $this->app['Validator'];

        $validator::extend('asirra', function($attr, $value, $options) {
            $asirra = new AsirraValidator;
            return $asirra->checkTicket($value);

        });
    }

    public function createFormMacro()
    {
        app('form')->macro('asirra', function() {
            $view = 'asirra::template';

            return app('view')->make($view);
        });
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}