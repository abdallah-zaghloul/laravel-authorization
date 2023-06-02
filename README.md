<pre>

# laravel-authorization
Highly performant simple scalable dynamic database-query-optimized laravel authorization package

# Install
composer require zaghloul-soft/laravel-authorization
php artisan role:model
php artisan vendor:publish --provider="ZaghloulSoft\LaravelAuthorization\AuthorizationServiceProvider"


# Commands
- publish Role Model
php artisan role:model

- link role to another model
php artisan role:relation ModelName

- seed roles
php artisan role:seed

- publish all files (config,migrations,route,controller,middleware)
php artisan vendor:publish --provider="ZaghloulSoft\LaravelAuthorization\AuthorizationServiceProvider"

- publish some files by tages
php artisan vendor:publish --tag=role-config
php artisan vendor:publish --tag=role-migrations
php artisan vendor:publish --tag=role-messages
php artisan vendor:publish --tag=role-middlewares
php artisan vendor:publish --tag=role-controllers
php artisan vendor:publish --tag=role-routes

</pre>
