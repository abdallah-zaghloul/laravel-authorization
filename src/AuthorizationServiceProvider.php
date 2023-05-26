<?php /** @noinspection PhpLanguageLevelInspection */

namespace ZaghloulSoft\LaravelAuthorization;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use ZaghloulSoft\LaravelAuthorization\Console\RoleSeederCommand;
use ZaghloulSoft\LaravelAuthorization\Console\ToggleRelationCommand;
use ZaghloulSoft\LaravelAuthorization\Console\RoleModelCommand;
use ZaghloulSoft\LaravelAuthorization\Middleware\LaravelAuthorization;
use ZaghloulSoft\LaravelAuthorization\Utilities\Role;
use ZaghloulSoft\LaravelAuthorization\Facades\Role as RoleFacade;
use function app;
use function collect;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        app('router')->aliasMiddleware('laravel.authorization', LaravelAuthorization::class);
        $this->loadRoutesFrom(__DIR__."/routes/roles.php");

        $this->commands([
            RoleModelCommand::class,
            ToggleRelationCommand::class,
            RoleSeederCommand::class,
        ]);

        $this->publishes([
            __DIR__."/config/".($config = "laravel-authorization.php") => config_path($config),
            __DIR__."/database/migrations/".($migration = "2015_03_13_163576_create_roles_table.php") => database_path("migrations/$migration"),
            __DIR__."/messages/".($messages = "laravel-authorization.php") => resource_path("lang/en/$messages"),
            __DIR__."/Middleware/".($middleware = "LaravelAuthorization.php") => app_path("Http/Middleware/$middleware"),
            __DIR__."/Controllers/".($controller = "RolesController.php") => app_path("Http/Controllers/$controller"),
            __DIR__."/routes/".($route = "roles.php") => base_path("routes/$route"),
        ],['role-config','role-migrations','role-messages','role-middlewares','role-controllers','role-routes']);

    }

    public function register()
    {
        $this->app->bind('Role',fn() => app(Role::class));
        AliasLoader::getInstance()->alias('Role', RoleFacade::class);
    }
}
