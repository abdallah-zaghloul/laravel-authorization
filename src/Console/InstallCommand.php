<?php
/** @noinspection PhpLanguageLevelInspection */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */

namespace ZaghloulSoft\LaravelAuthorization\Console;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:install';

    /**
     * The publishes files keyed by tags
     *
     * @var Collection
     */
    protected static ?Collection $publishes;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ZaghloulSoft LaravelAuthorization Install Command';

    public function __construct()
    {
        parent::__construct();
        static::$publishes = static::getPublishes();
    }

    public function handle()
    {
        Artisan::call('config:cache');
        Artisan::call('role:model');
        Artisan::call("vendor:publish --tag=\"".static::$publishes->except('role-config')->keys()->implode('|'));
        Artisan::call('migrate');
        Artisan::call("vendor:publish --tag=\"role-config\"");

        $publishFiles = static::getPublishFiles();
        $this->info("the following files has been published :"
            ."\n"."------------------------------------------------"
            ."\n"."{$publishFiles->map(fn($value,$key) => "$key => $value")->implode("\n")}"
            ."\n"
            ."\n"."{$publishFiles->filter(fn($value) => Str::contains($value,'create_roles_table'))->values()->implode('')} migrated successfully"
            ."\n"."Zaghloul Soft Laravel Authorization Installed Successfully"
        );
    }

    public static function getPublishes() : Collection
    {
        return collect([
            'role-config'=> [__DIR__."/../config/".($config = "laravel-authorization.php") => config_path($config)],
            'role-migrations'=> [__DIR__."/../database/migrations/".($migration = "2015_03_13_163576_create_roles_table.php") => database_path("migrations/$migration")],
            'role-messages'=> [__DIR__."/../messages/".($messages = "laravel-authorization.php") => resource_path("lang/en/$messages")],
            'role-middlewares'=> [__DIR__."/../Middleware/".($middleware = "LaravelAuthorization.php") => app_path("Http/Middleware/$middleware")],
            'role-controllers'=> [__DIR__."/../Controllers/".($controller = "RolesController.php") => app_path("Http/Controllers/$controller")],
            'role-routes'=> [__DIR__."/../routes/".($route = "roles.php") => base_path("routes/$route")]
        ]);
    }

    public static function getPublishFiles() : Collection
    {
        return static::getPublishes()->mapWithKeys(fn($value,$key) => $value);
    }
}
