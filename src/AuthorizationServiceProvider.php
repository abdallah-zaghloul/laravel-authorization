<?php
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpLanguageLevelInspection */

namespace ZaghloulSoft\LaravelAuthorization;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Throwable;
use ZaghloulSoft\LaravelAuthorization\Console\InstallCommand;
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
    /**
     * @throws Throwable
     */
    public function boot()
    {
        app('router')->aliasMiddleware('laravel.authorization', LaravelAuthorization::class);
        $this->loadRoutesFrom(__DIR__."/routes/roles.php");

        $this->commands([
            RoleModelCommand::class,
            ToggleRelationCommand::class,
            RoleSeederCommand::class,
            InstallCommand::class,
        ]);

        $this->publishes(InstallCommand::getPublishFiles()->all() , InstallCommand::getPublishes()->keys()->all());
        RoleFacade::validateConfig();
    }

    public function register()
    {
        $this->app->bind('Role',fn() => app(Role::class));
        AliasLoader::getInstance()->alias('Role', RoleFacade::class);
    }
}
