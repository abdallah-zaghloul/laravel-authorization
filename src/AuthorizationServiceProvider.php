<?php /** @noinspection PhpLanguageLevelInspection */

namespace ZaghloulSoft\LaravelAuthorization;
use Illuminate\Support\ServiceProvider;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use function app;
use function collect;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutes();
    }

    public function register()
    {
        $this->app->bind('Roles',fn() => app(Role::class));
    }

    public function loadRoutes() : void
    {
        collect([
            'web.php',
            'api.php'
        ])->each(fn($route) => $this->loadRoutesFrom(__DIR__."/../routes/$route"));
    }
}
