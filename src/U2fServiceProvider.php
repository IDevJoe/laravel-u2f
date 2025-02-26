<?php namespace Lahaxearnaud\U2f;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class U2fServiceProvider extends ServiceProvider
{

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
    public function boot(Router $router)
    {
        $routeConfig = [
            'namespace' => '\Lahaxearnaud\U2f\Http\Controllers',
            'prefix' => '/u2f/',
            'middleware' => $this->app[ 'config' ]->get('u2f.authMiddlewareName', 'web')
        ];

        $this->app[ 'router' ]->group($routeConfig, function(Router $router) {
            $router->get('register', [
                'uses' => 'U2fController@registerData',
                'as' => 'u2f.register.data',
               'middleware' => 'auth'
            ]);
            $router->post('register', [
                'uses' => 'U2fController@register',
                'as' => 'u2f.register',
               'middleware' => 'auth'
            ]);

            $router->get('auth', [
                'uses' => 'U2fController@authData',
                'as' => 'u2f.auth.data',
               'middleware' => 'auth'
            ]);
            $router->post('auth', [
                'uses' => 'U2fController@auth',
                'as' => 'u2f.auth',
               'middleware' => 'auth'
            ]);
        });


        $this->publishes([
            __DIR__ . '/../database/migrations/' => base_path('/database/migrations')
        ], 'u2f-migrations');

        $this->publishes([
            __DIR__ . '/../config/u2f.php' => config_path('u2f.php')
        ], 'u2f-config');

        $this->publishes([
            __DIR__. '/../resources/js' => public_path('vendor/u2f'),
        ], 'u2f-components');

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/u2f'),
        ], 'u2f-views');

        $this->loadViewsFrom(__DIR__ . '/../views/', 'u2f');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'u2f');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
        $this->app->bind('u2f', function() use ($app){

            return new U2f($app->make('config'), $this->app->make('session'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ 'u2f' ];
    }
}
