# laravel-authorization <br>
Highly performant simple scalable dynamic database-query-optimized laravel authorization package <br>

# Install <br>
composer require zaghloul-soft/laravel-authorization <br>
php artisan role:install <br>

# Link Relation <br>
php artisan role:relation $modelName <br>

# Seed Roles <br>
php artisan role:seed


# Other Manual Commands <br>
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

new
