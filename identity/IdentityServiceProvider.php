<?php

namespace JPuminate\Auth\Identity;

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use JPuminate\Auth\Identity\Guards\IdentityUserGuard;
use JPuminate\Auth\Identity\Guards\TokenGuard;

class IdentityServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if(App::make(AuthConfig::class.'-users')->isHosted())  $this->registerMigrations();
            $this->_publishes();
            $this->commands([
                Console\PublicKeyCommand::class
            ]);
        }
    }

    public function register(){
        $this->registerAuthGateWay();
        $this->registerAuthConfig();
        $this->registerUserGuards();
    }

    private function registerUserGuards()
    {
        Auth::extend('identity', function ($app, $name, array $config) {
            return new TokenGuard(
                Auth::createUserProvider($config['provider']),
                $this->app->make(AuthGateway::class),
                $app['request'],
                $this->app->make(AuthConfig::class.'-users'),
                $this->app->make(AuthConfig::class.'-clients'));
        });
    }

    protected function registerMigrations()
    {
        if (Identity::$runsMigrations) {
            return $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'identity-migrations');
    }

    private function _publishes(){
        $this->publishes([
            __DIR__ . '/../resources/config/identity.php' => config_path('identity.php'),
        ], 'config');
    }


    protected function registerAuthGateWay(){
        $this->app->singleton(AuthGateway::class, function(){
            return tap(new AuthGateway(), function(AuthGateway $gateway){
                $gateway->setPublicKey(file_get_contents(Identity::keyPath('oauth-public.key')));
                $gateway->setEncrypter($this->app->make('encrypter'));
            });
        });
    }

    protected function registerAuthConfig(){
        $this->app->singleton(AuthConfig::class.'-users', function(){
            return new AuthConfig(config(Identity::$configFile.'.users'));
        });
        $this->app->singleton(AuthConfig::class.'-clients', function(){
            return new AuthConfig(config(Identity::$configFile.'.clients'));
        });

       $this->app->bind(AuthDomain::class, function(){
           try {
               if(request()->user()) return request()->user()->domain();
               else return new AuthDomain(AuthDomain::$NO_ONE);
           }
           catch(\Exception $e){
               return new AuthDomain(AuthDomain::$NO_ONE);
           }
        });
    }



}
