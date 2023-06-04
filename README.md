# laravel-authorization <br>
Highly performant simple scalable dynamic database-query-optimized laravel authorization package <br>

# Install <br>
composer require zaghloul-soft/laravel-authorization <br>
php artisan role:model <br>
php artisan vendor:publish --provider="ZaghloulSoft\LaravelAuthorization\AuthorizationServiceProvider" <br>


# Commands <br>

<br>
* publish Role Model <br>
php artisan role:model <br>
<br>
* link role to another model <br>
php artisan role:relation ModelName <br>
<br>
* seed roles <br>
php artisan role:seed <br>
<br>
* publish all files (config,migrations,route,controller,middleware) <br>
php artisan vendor:publish --provider="ZaghloulSoft\LaravelAuthorization\AuthorizationServiceProvider" <br>
<br>
* publish some files by tages <br>
php artisan vendor:publish --tag=role-config <br>
php artisan vendor:publish --tag=role-migrations <br>
php artisan vendor:publish --tag=role-messages <br>
php artisan vendor:publish --tag=role-middlewares <br>
php artisan vendor:publish --tag=role-controllers <br>
php artisan vendor:publish --tag=role-routes <br>

