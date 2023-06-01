<?php
/** @noinspection PhpLanguageLevelInspection */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */

namespace ZaghloulSoft\LaravelAuthorization\Console;
use Illuminate\Console\GeneratorCommand;

class RoleModelCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:model {name=Role} --force=false';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish ZaghloulSoft LaravelAuthorization Role Model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . "/DummyClass.php";
    }

   protected function getDefaultNamespace($rootNamespace)
   {
       return is_dir(app_path('Models')) ? "$rootNamespace\\Models" : $rootNamespace;
   }
}
