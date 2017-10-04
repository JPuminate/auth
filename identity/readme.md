## Introduction
JP Identity is an API for checking authorization token locally.

### Installation
To get started, install Identity via the Composer package manager:

```php
composer require jpuminate/auth 
```
Next, register the Identity service provider in the providers array of your config/app.php configuration file:

```php
JPuminate\Auth\Identity\IdentityServiceProvider::class,
```

The Identity service provider registers its own database migration directory with the framework, so you should migrate your database after registering the provider. The Identity migrations will update users table if exist:

```php
php artisan migrate
```

If you are not going to use Identity's default migrations, you should call the  Identity::ignoreMigrations method in the register method of your  ```AppServiceProvider```. You may export the default migrations using  
```php
php artisan vendor:publish --provider="JPuminate\Auth\Identity\IdentityServiceProvider" --tag=identity-migrations
```

After running this command, make the your App\User model use  JPuminate\Auth\Identity\HasOAuthToken trait.

```php
<?php

namespace App;

use Illuminate\Notifications\Notifiable

class User extends Model
{
    use Notifiable, HasOAuthToken;
}
```
Next, you should publish the Identity config file. by running this command:

```php
php artisan vendor:publish --provider="JPuminate\Auth\Identity\IdentityServiceProvider" --tag=config
```

In your config/auth.php configuration file, you should set the driver option of the api authentication guard to **identity**.
 This will instruct your application to use Identity's Guard when authenticating incoming API requests:
```php
'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'identity',
            'provider' => 'users',
        ],
    ],
```

Finally, Identity needs a public-key to verify tokens, so run this command :

```php
 php artisan identity:public-key
```
This command generate a file that will contain your public-key's auth server in your storage path. 
                                                  
                                             
### Configuration

#### Authentication domains
The config/identity.php exports two domains of authentication: users and third-parties(clients). <br/>
the users domain allows you to configure a part of your application that reachable by the Users <br/>
and the third-parties domain helps you to manage the requests coming from the microservice instances or other clients hosted in  Auth Server

```php
<?php

return [
    "users" => [
       "subject" => [
               "hosted" => true, // if you have your own users in your microservice
               ],
       "claims" => [ // extracted from the token
               "username" => "username", 
               "phone" => "phone",
               "scopes" => "scopes" // if you need to manage scopes
               ]
    ],
    "clients" => [
       "claims" => [ // extracted from the token
               "name" => "client_name",
               "scopes" => "scopes" // if you need to manage scopes
               ]
     ]
];
```

### Users Table
By defaults, identity use 'users' as table name, and jp_user_id as foreign key, 
If you would like to configure them, you may use the **foreignKey** and  **usersTable** methods,  
These methods should be called from the boot method of your  **AuthServiceProvider**:

```php
public function boot()
{
    $this->registerPolicies();

    Identity::usersTable('x_users');
    
    Identity::foreignKey('oauth_user_id');
}
```

### Protecting Routes

#### Guard

Identity includes an authentication guard that will validate access tokens on incoming requests. 
Once you have configured the api guard to use the identity driver, 
you only need to specify the  auth:api middleware on any routes that require a valid access token:

```php
Route::get('/user', function () {
    //
})->middleware('auth:api');
```
When calling routes that are protected by Identity, your application's API consumers should specify their 
access token as a Bearer token in the Authorization header of their request.

#### Checking Scopes
When using scopes as claim value in your identity config file, you will be able to manage scopes in your microservice api,
To get started, Identity comes with a scope checker middleware, all you need is to put it in the 
*$routeMiddleware* property of your *app/Http/Kernel.php* file:

```php
 protected $routeMiddleware = [
       // others
        'check_scopes' => \JPuminate\Auth\Identity\Http\Middleware\CheckScopes::class,
    ];
```
To use it:

```php
use Illuminate\Http\Request;

Route::get('/vehicles', function () {
    // Access token has both "check-status" and "access_vehicles" scopes...
})->middleware('check_scopes:check-status,access_vehicles');
```

##### Checking Scopes On A Token Instance

Once an access token authenticated request has entered your application, you may still check if the token has
a given scope using the tokenCan method on the authenticated User/Client instance:

```php
use Illuminate\Http\Request;

Route::get('/trackings', function (Request $request) {
    if ($request->user()->tokenCan('access-orders')) {
        //
    }
});
```

#### Checking domains

if your microservice api can be consumed by the users and the clients, so you have to check the domain that the current
user is belongs to, Identity comes with that checker, all you need is to put this middleware in your *app/Http/Kernel.php* file:

```php
 protected $routeMiddleware = [
        // others
        'check_domain' => \JPuminate\Auth\Identity\Http\Middleware\CheckAuthDomain::class,
    ];
```

To use it:

```php
use Illuminate\Http\Request;

Route::get('/users/id/vehicles', function () {
    // Access only for the users
})->middleware('check_domain:AuthDomain::$USERS');
```

```php
use Illuminate\Http\Request;

Route::get('/trackings', function () {
     // Access only for the clients
})->middleware('check_domain:AuthDomain::$CLIENTS');
```

## Events

Identity raises event when create hosted users if it's possible. 
You may use these event to add defaults data to your model. 
You may attach listeners to these events in your application's ```EventServiceProvider```:

```php
/**
 * The event listener mappings for the application.
 *
 * @var array
 */
 
  protected $listen = [
        'JPuminate\Auth\Identity\Events\HostedUserCreated' => [
            'App\Listeners\HostedUserCreatedListener',
        ],
    ];
```
