<?php /** @noinspection PhpUndefinedMethodInspection */

namespace ZaghloulSoft\LaravelAuthorization\Utilities;

use Error;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HigherOrderWhenProxy;
use Illuminate\Support\Str;
use Throwable;
use Traversable;
use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;

class Role
{
    protected Collection $config;
    protected Collection $authGuards;
    protected Collection $moduledPermissionsNames;
    protected RoleModel $role;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->moduledPermissionsNames = $this->getModuledPermissionsNames();
        $this->role = $this->getRole();
        $this->authGuards = $this->authGuards();
    }


    /**
     * @return Collection
     */
    public function getConfig(): Collection
    {
        return collect(config('laravel-authorization'));
    }


    /**
     * @return string|null
     */
    public function superRoleGuard(): ?string
    {
        return $this->config->get('superRoleGuard');
    }


    /**
     * @return string|null
     */
    public function superRoleName(): ?string
    {
        return $this->config->get('superRoleName','Super');
    }

    /**
     * @return string|null
     */
    public function getFirstSuperRoleGuardPrimaryKey(): ?string
    {
        return $this->config->get('firstSuperRoleGuardPrimaryKey','1');
    }


    /**
     * @return RoleModel
     */
    protected function getRole(): RoleModel
    {
        return app(RoleModel::class);
    }

    /**
     * @param string $guard
     * @return Builder
     */
    protected function queryRolesExceptSuper(string $guard): Builder
    {
        return $this->role->query()->where([
            ['guard',$guard],
            ['name','!=', $this->superRoleName()]
        ]);
    }

    /**
     * @return Collection
     */
    public function getModuledPermissionsNames(): Collection
    {
        return collect(config('laravel-authorization.moduled_permissions'));
    }

    /**
     * @return Collection
     */
    public function getGuards(): Collection
    {
        return $this->getModuledPermissionsNames()->keys();
    }

    /**
     * @return Collection
     */
    public function authGuards(): Collection
    {
        return collect(config('auth.guards'));
    }

    /**
     * @return Collection
     */
    public function invalidGuards(): Collection
    {
        return $this->getGuards()->diff($this->authGuards->keys());
    }

    /**
     * @return int
     */
    public function getPaginationCount(): int
    {
        return $this->config->get('paginationCount',50);
    }


    /**
     * @param bool $canAccess
     * @return Collection
     */
    public function getModuledPermissions(bool $canAccess = false): Collection
    {
        return $this->moduledPermissionsNames->mapWithKeys(fn($moduledPermissionsNames,$guard) => [$guard => $this->getGuardModuledPermissions($guard,$canAccess)]);
    }

    /**
     * @param string $guard
     * @param bool $canAccess
     * @return Collection
     */
    public function getGuardModuledPermissions(string $guard, bool $canAccess = false): Collection
    {
        $guardModuledPermissionsNames = $this->getGuardModuledPermissionsNames($guard);
        return collect($guardModuledPermissionsNames)->transform(fn($permissionsNames,$module) => array_fill_keys($permissionsNames,$canAccess));
    }

    /**
     * @param string $guard
     * @return array|null
     */
    public function getGuardModuledPermissionsNames(string $guard) : ?array
    {
        return $this->moduledPermissionsNames->get($guard);
    }


    /**
     * @return void
     */
    public function seedSuperRole()
    {
        try {
            DB::beginTransaction();
            $superRole = $this->role->updateOrCreate([
                'name'=> $this->superRoleName(),
                'guard'=> ($superRoleGuard = $this->superRoleGuard())
            ],['moduled_permissions'=> $this->getGuardModuledPermissions($superRoleGuard,true)]);
            $this->assignSuperRoleToFirstGuard($superRoleGuard,$superRole);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
        }
    }

    /**
     * @param string|null $superRoleGuard
     * @param RoleModel $superRole
     * @return void
     */
    protected function assignSuperRoleToFirstGuard(?string $superRoleGuard , RoleModel $superRole)
    {
        $table = @$this->getGuardTable($superRoleGuard) ?? '';
        $guardPrimaryKeyColumn = @$this->getGuardPrimaryKeyColumn($table) ?? '';

        if (Schema::hasColumn($table, $guardPrimaryKeyColumn) && Schema::hasColumn($table,'role_id')){
            $super = DB::table($table)->where($guardPrimaryKeyColumn,'=',$this->getFirstSuperRoleGuardPrimaryKey())->whereNull('role_id');
            $super->exists() and $super->update(['role_id'=> $superRole->getKey()]);
        }
    }

    /**
     * @param string $guardTable
     * @param string $guardPrimaryKeyColumn
     * @param string $guardPrimaryKey
     * @param string $roleId
     * @return bool
     */
    public function assign(string $guardTable,string $guardPrimaryKeyColumn,string $guardPrimaryKey,string $roleId): bool
    {
        return Schema::hasColumn($guardTable,'role_id') and DB::table($guardTable)->where($guardPrimaryKeyColumn,'=', $guardPrimaryKey)->update(['role_id'=> $roleId]);
    }


    /**
     * @param string $guard
     * @return void
     */
    public function seedGuardModuledPermissions(string $guard)
    {
        [$guardModuledPermissions , $guardRoles] = [$this->getGuardModuledPermissions($guard) , $this->queryRolesExceptSuper($guard)];

        $rolesUpdates = $guardRoles->pluck('moduled_permissions','id')->transform(fn (Collection $rowModuledPermissions,$id) => [
            'id'=> $id,
            'case'=> "WHEN id = $id THEN '{$this->correctModules($rowModuledPermissions,$guardModuledPermissions)->toJson()}'",
        ]);

        $rolesUpdates->whenNotEmpty(fn() => $this->bulkCaseUpdate($this->role->getTable(),'moduled_permissions',$rolesUpdates->pluck('case'), $rolesUpdates->pluck('id')));
    }

    /**
     * @param Collection $rowModuledPermissions
     * @param Collection $guardModuledPermissions
     * @return Collection|HigherOrderWhenProxy|mixed
     */
    protected function correctModules(Collection $rowModuledPermissions , Collection $guardModuledPermissions)
    {
        [$extraModules , $missingModules] = [$rowModuledPermissions->diffKeys($guardModuledPermissions)->keys() , $guardModuledPermissions->diffKeys($rowModuledPermissions)->keys()];
        return $rowModuledPermissions->when($extraModules->isNotEmpty(),fn($rowModuledPermissions) => $rowModuledPermissions->reject(fn($permissions,$module) => $extraModules->contains($module)))
            ->when($missingModules->isNotEmpty(),fn($rowModuledPermissions) => $rowModuledPermissions->merge($guardModuledPermissions->only($missingModules)))
            ->map(fn($permissions,$module) => $this->correctPermissions($module,$permissions,$guardModuledPermissions));
    }


    /**
     * @param $module
     * @param $permissions
     * @param Collection $guardModuledPermissions
     * @return Collection|HigherOrderWhenProxy|mixed
     */
    protected function correctPermissions($module , $permissions , Collection $guardModuledPermissions)
    {
        [$permissions , $default] = [collect($permissions) , collect($guardModuledPermissions->get($module))];
        $extraPermissions = $permissions->diffKeys($default)->keys();
        $permissions = $permissions->reject(fn($canAccess,$permission) => (! $this->validCanAccess($canAccess)) or $extraPermissions->contains($permission));
        $missingPermissions = $default->diffKeys($permissions)->keys();
        return $permissions->when($missingPermissions->isNotEmpty(),fn($permissions) => $permissions->merge($default->only($missingPermissions)));
    }


    /**
     * @param $canAccess
     * @return bool
     */
    public function validCanAccess($canAccess): bool
    {
        return (! is_numeric($canAccess)) && is_bool($canAccess);
    }


    /**
     * @return void
     */
    public function seedMissingGuardsModuledPermissions()
    {
        [$dbGuards,$configGuards] = [$this->role->select('guard')->distinct()->pluck('guard'), $this->moduledPermissionsNames->keys()];
        [$extraGuards,$missingGuards] = [$dbGuards->diff($configGuards),$configGuards->diff($dbGuards)];

        $missingGuards->whenNotEmpty(fn(Collection $missingGuards) => $missingGuards->each(fn($guard) => $this->role->updateOrCreate([
            'name'=> ucfirst($guard),
            'guard'=> $guard
        ],['moduled_permissions'=> $this->getGuardModuledPermissions($guard)])));

        $extraGuards->whenNotEmpty(fn(Collection $extraGuards) => $this->role->whereIn('guard',$extraGuards->all())->delete());
    }


    /**
     * @param string|null $guard
     * @return void
     */
    public function seed(?string $guard = null)
    {
        $this->seedSuperRole();
        $guard ? $this->seedGuardModuledPermissions($guard) : $this->moduledPermissionsNames->keys()->each(fn($guard) => $this->seedGuardModuledPermissions($guard));
        $this->seedMissingGuardsModuledPermissions();
    }


    /**
     * @param string $table
     * @param string $column
     * @param string|Traversable $cases #[ExpectedValues("WHEN $valid_sql_condition THEN $update_value")]
     * @param Traversable $uniqueByValues
     * @param string $uniqueByColumn
     * @return void
     */
    protected function bulkCaseUpdate(string $table, string $column, $cases , Traversable $uniqueByValues, string $uniqueByColumn = 'id')
    {
        try {
            DB::beginTransaction();
            $cases instanceof Traversable and $cases = $this->traversableToString($cases,' ');
            DB::statement("UPDATE $table SET $column = (CASE $cases END) WHERE $uniqueByColumn IN ({$this->traversableToString($uniqueByValues)});");
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
        }
    }


    /**
     * @param Traversable $traversable
     * @param string $separator
     * @return string
     */
    protected function traversableToString(Traversable $traversable, string $separator = ','): string
    {
        return implode($separator,iterator_to_array($traversable));
    }


    /**
     * @return string|null
     */
    public function getCurrentGuard() : ?string
    {
        return @collect(config('auth.guards'))->keys()->filter(fn($guard) => auth($guard)->check())->first();
    }


    /**
     * @param string $table
     * @param string $onDelete
     * @return void
     */
    public function linkRelation(string $table, string $onDelete = 'set null')
    {
        if (Schema::hasTable($table) && ! Schema::hasColumn($table,'role_id'))
        Schema::table($table,fn (Blueprint $table) => $table->foreignId('role_id')->nullable()->constrained()->onDelete($onDelete)->cascadeOnUpdate());
    }


    /**
     * @param string $table
     * @return void
     */
    public function unLinkRelation(string $table)
    {
        if (Schema::hasTable($table) && Schema::hasColumn($table,'role_id'))
        {
            $roleIdForeignKeyIndex = $this->getRoleIdForeignKeyIndex($table);
            !empty($roleIdForeignKeyIndex) and Schema::table($table, fn(Blueprint $table) => $table->dropForeign($roleIdForeignKeyIndex));
            Schema::table($table,fn(Blueprint $table) => $table->dropColumn('role_id'));
        }
    }


    /**
     * @param string $table
     * @return mixed
     */
    protected function getRoleIdForeignKeyIndex(string $table)
    {
        return $this->getForeignKeysIndexes($table)->get('role_id');
    }

    /**
     * @param string $guard
     * @return mixed
     */
    public function getGuardTable(string $guard)
    {
        return @$this->authGuards->get($guard)['provider'];
    }

    /**
     * @param string $table
     * @return mixed
     */
    public function getGuardPrimaryKeyColumn(string $table)
    {
        return $this->getTableIndexes($table)->filter(fn($index) => Str::contains($index,'PRIMARY'))->keys()->first();
    }

    /**
     * @param string $table
     * @return Collection
     */
    protected function getTableIndexes(string $table): Collection
    {
        return collect(DB::select("SHOW INDEX FROM $table;"))->pluck('Key_name','Column_name');
    }

    /**
     * @param string $table
     * @return Collection
     */
    protected function getForeignKeysIndexes(string $table): Collection
    {
        return $this->getTableIndexes($table)->filter(fn($index) => Str::contains($index,'foreign'));
    }


    /**
     * @return string
     */
    public function nowInMigrationRegex(): string
    {
        $now = now()->format('Y-m-d H:i:s');
        [$date,$time] = [substr($now,0,10),substr($now,11)];
        [$dateMigrationRegex,$timeMigrationRegex] = [preg_replace('/-|:|\s+/','_', $date), preg_replace('/-|:|\s+/','',$time)];
        return $dateMigrationRegex.'_'.$timeMigrationRegex;
    }

    /**
     * @throws Throwable
     */
    public function validateConfig()
    {
        [$packageConfig,$authConfig] = [config_path('laravel-authorization.php'),config_path('auth.php')];
        if (file_exists($packageConfig)){
            $this->checkForInvalidGuards($packageConfig,$authConfig);
            $this->checkForInvalidModules($packageConfig);
        }
    }

    /**
     * @throws Throwable
     */
    protected function checkForInvalidGuards($packageConfig,$authConfig)
    {
        $condition = ( $invalidGuards = $this->invalidGuards() )->isNotEmpty();
        $message = "You have invalid guards ({$invalidGuards->implode(',')}) at $packageConfig differ than your auth config guards at $authConfig";
        throw_if($condition,new Error($message));
    }

    /**
     * @throws Throwable
     */

    protected function checkForInvalidModules($packageConfig)
    {
        $condition = ( $invalidModules = $this->invalidModules() )->isNotEmpty();
        $message = "You have invalid modules (\n\n".Str::replace(["{","}",":"],["","\n",' => '],$invalidModules->toJson()).") at $packageConfig";
        throw_if($condition,new Error($message));
    }


    /**
     * @return Collection
     */
    protected function invalidModules(): Collection
    {
        $invalids = collect();
        foreach ($this->moduledPermissionsNames as $guard => $modules){
           collect($modules)->each(function($permissions,$moduleName) use ($guard,$invalids){
               $permissions = collect($permissions);
               !$this->validName($moduleName) and $this->assignInvalids($invalids,$guard,$moduleName);
               $validPermissions = $permissions->filter(fn($permission) => $this->validName($permission));
               $invalidPermissions = $permissions->diffKeys($validPermissions)->flatten();
               $invalidPermissions->isNotEmpty() and $this->assignInvalids($invalids,$guard,$moduleName,$invalidPermissions->all());
           });
       }
        return $invalids;
    }

    /**
     * @param Collection $iterable
     * @param $guard
     * @param $module
     * @param $permissions
     * @return Collection|HigherOrderWhenProxy|mixed
     */
    protected function assignInvalids(Collection $iterable, $guard, $module, $permissions = null)
    {
       return $iterable->when(! $iterable->has($guard), fn(Collection $iterable) => $iterable->put($guard,null))
           ->when(empty($iterable->get($guard)),function (Collection $iterable) use ($guard,$module){
               $iterable[$guard] = [$module => null];
               return $iterable;
           })->when(!empty($permissions) && empty(@$iterable[$guard][$module]),function (Collection $iterable) use ($guard,$module,$permissions){
               $guardModules = $iterable[$guard];
               $guardModules[$module] = $permissions;
               $iterable[$guard] = $guardModules;
               return $iterable;
           });
    }

    /**
     * @param $name
     * @return bool
     */
    protected function validName($name): bool
    {
        return is_string($name) && !empty(@trim($name));
    }
}
