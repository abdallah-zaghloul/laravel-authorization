<?php
/** @noinspection PhpLanguageLevelInspection */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */

namespace ZaghloulSoft\LaravelAuthorization\Console;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use Illuminate\Console\GeneratorCommand;

class RoleSeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:seed {guard?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ZaghloulSoft LaravelAuthorization Role Seeder';

    public function handle()
    {
        Artisan::call('config:cache');
        Role::getGuards()->contains($guard = $this->argument('guard')) ? Role::seedGuardModuledPermissions($guard) : Role::seed();
        $this->info('Roles seeded successfully');
    }
}
