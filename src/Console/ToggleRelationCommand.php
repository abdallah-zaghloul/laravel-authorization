<?php

/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpLanguageLevelInspection */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */

namespace ZaghloulSoft\LaravelAuthorization\Console;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleStub;
class ToggleRelationCommand extends GeneratorCommand
{
    protected string $modelsDirNameSpace;
    protected string $modelsDirPath;
    protected string $migrationsDirPath;
    protected string $roleModelName;
    protected ?string $roleModelContent;

    /**
     * @throws FileNotFoundException
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);

        $this->modelsDirNameSpace = $this->getModelsDirNameSpace();
        $this->modelsDirPath = $this->getModelsDirPath();
        $this->migrationsDirPath = database_path('migrations');
        $this->roleModelName = 'Role';
        $this->roleModelContent = $this->getRoleModelContent();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:relation {name} {link=link} --force=true';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link table with ZaghloulSoft LaravelAuthorization Role table';


    public function handle()
    {
       $method = collect([
           'link'=> 'linkRelation',
//           'unlink'=> 'unlinkRelation'
       ])->get($this->argument('link'),'link');
       $this->{$method}();
    }

    /**
     * @throws FileNotFoundException
     */
    public function linkRelation()
    {
        $this->checkForOldMigrationFiles();
        $this->publishMigrationFile();
        $this->publishRoleModelFile();
        $this->publishModelFile();
    }

    public function unlinkRelation()
    {
        $this->oldRelationMigrationFiles()->each(function($migrationFileName) {
            Artisan::call("migrate:rollback --path=database/migrations/$migrationFileName");
            $this->files->delete("$this->migrationsDirPath/$migrationFileName");
        });
    }

    /**
     * @return Collection
     */
    public function oldRelationMigrationFiles() : Collection
    {
        return collect(scandir($this->migrationsDirPath))->filter(fn($migrationFileName) => Str::contains($migrationFileName,$this->migrationRelationFileRegex()));
    }

    public function checkForOldMigrationFiles()
    {
        return ($old = $this->oldRelationMigrationFiles())
            ->whenNotEmpty(fn() => $this->error("Please migrate:rollback and delete Old Relation migration files that you have called : \n{$old->implode("\n")}"));
    }

    /**
     * @throws FileNotFoundException
     */
    public function publishMigrationFile()
    {
        $migrationFilePath = $this->migrationsDirPath . '/' . $this->getNameInput().'.php';
        $contents = $this->replaceClass($this->files->get($this->getStub()));
        ! $this->files->exists($migrationFilePath) ? $this->publishFile($migrationFilePath,$contents) : $this->error("File :{$migrationFilePath} already exists");
    }

    /**
     * @throws FileNotFoundException
     */
    public function publishModelFile()
    {
        $modelFilePath = "$this->modelsDirPath/{$this->getModelName()}.php";
        $this->publishFile($modelFilePath, $this->setModelContent());
    }

    public function publishRoleModelFile()
    {
        $roleModelFilePath = "$this->modelsDirPath/$this->roleModelName.php";
        $this->publishFile($roleModelFilePath, $this->setRoleModelContent());
    }


    public function publishFile($path,$contents)
    {
        $this->files->put($path,$contents);
        $this->info("File :{$path} Updated Successfully");
    }

    public function getTable()
    {
        return $this->getModel()->getTable();
    }

    public function migrationRelationFileRegex()
    {
        return "link_{$this->getTable()}_to_roles_table";
    }

    public function getNameInput()
    {
        $stubFileName = Str::remove('.php',Str::afterLast($this->getStub(),'/'));
        return Str::replace([
            'date_time',
            'dummy_table',
        ],[
            Role::nowInMigrationRegex(),
            $this->getTable()
        ], $stubFileName);
    }

    protected function replaceClass($stub, $name = null)
    {
        $table = $this->getTable();
        return Str::replace([
            "DummyClass",
            "dummyTable",
        ],[
            "Link".Str::studly($table)."ToRolesTable",
            $table,
        ],$stub);
    }

    protected function getModelName()
    {
        $name = $this->argument('name');
        $modelClassName = Str::contains($name,$ext = '.php') ? Str::replace($ext,'',$name) : $name;
        return Str::studly($modelClassName);
    }

     protected function getModel(): ?Model
    {
        return @app("$this->modelsDirNameSpace\\{$this->getModelName()}");
    }

    protected function getModelsDirNameSpace():string
    {
        return $this->files->isDirectory(app_path('Models')) ? "App\\Models" : "App";
    }

    protected function getModelsDirPath():string
    {
        return $this->files->isDirectory($dir = app_path('Models')) ? $dir : app_path();
    }

    protected function getStub()
    {
       return __DIR__."/date_time_link_dummy_table_to_roles_table.php";
    }


    /**
     * @throws FileNotFoundException
     */
    public function setModelContent()
    {
        $relation = "\n    public function role() \n    {\n        return " .'$this->belongsTo(Role::class);'."\n    }\n}";
        $modelContent = $this->getModelContent();
        return (! Str::contains(trim($modelContent),'role()')) ? Str::replaceLast('}', $relation,$modelContent) : $modelContent;
    }

    public function setRoleModelContent()
    {
        [$modelName , $relationMethod] = [$this->getModelName() , Str::camel(Str::plural($this->getTable()))];

        $needles = [$relationMethod , Str::snake($relationMethod)];
        $relation = "\n    public function $relationMethod() \n    {\n        return ".'$this->hasMany('.$modelName.'::class);'."\n    }\n}";
        return $this->roleModelContent = (! Str::contains(trim($this->roleModelContent),$needles)) ? Str::replaceLast('}', $relation, $this->roleModelContent) : $this->roleModelContent;
    }

    /**
     * @throws FileNotFoundException
     */
    protected function getRoleModelContent()
    {
        return $this->files->exists("$this->modelsDirPath/$this->roleModelName.php") ? $this->files->get("$this->modelsDirPath/$this->roleModelName.php") : $this->defaultRoleModelContent();
    }

    /**
     * @throws FileNotFoundException
     */
    protected function getModelContent()
    {
        return $this->files->get("$this->modelsDirPath/{$this->getModelName()}.php");
    }

    /**
     * @throws FileNotFoundException
     */
    protected function defaultRoleModelContent()
    {
        return Str::replace([
            'DummyNamespace',
            'DummyClass',
        ],[
            $this->modelsDirNameSpace,
            'Role'
        ],$this->files->get(__DIR__."/../Console/DummyClass.php"));
    }
}
