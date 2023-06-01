<?php /** @noinspection PhpLanguageLevelInspection */

namespace ZaghloulSoft\LaravelAuthorization;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use ZaghloulSoft\LaravelAuthorization\Console\ToggleRelationCommand;
use ZaghloulSoft\LaravelAuthorization\Console\MakeRoleModelCommand;
use ZaghloulSoft\LaravelAuthorization\Middleware\LaravelAuthorization;
use ZaghloulSoft\LaravelAuthorization\Utilities\Role;
use ZaghloulSoft\LaravelAuthorization\Facades\Role as RoleFacade;
use function app;
use function collect;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__."/routes/roles.php");
        $this->app->bind('Role',fn() => app(Role::class));

        $this->commands([
            MakeRoleModelCommand::class,
            ToggleRelationCommand::class,
        ]);

        $this->publishes([
            __DIR__."/config/".($config = "laravel-authorization.php") => config_path($config),
            __DIR__."/database/migrations/".($migration = "2015_03_13_163576_create_roles_table.php") => database_path("migrations/$migration"),
            __DIR__."/database/seeders/".($seeder = "SuperRoleSeeder.php") => database_path("seeders/$seeder"),
            __DIR__."/messages/".($messages = "laravel-authorization.php") => resource_path("lang/en/$messages"),
            __DIR__."/Middleware/".($middleware = "LaravelAuthorization.php") => app_path("Http/Middleware/$middleware"),
            __DIR__."/Controllers/".($controller = "RolesController.php") => app_path("Http/Controllers/$controller"),
        ],['config','migrations','seeders','messages','middlewares','controllers']);

    }

    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Role', RoleFacade::class);
        app('router')->aliasMiddleware('laravel.authorization', LaravelAuthorization::class);
    }
}
