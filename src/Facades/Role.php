<?php

namespace ZaghloulSoft\LaravelAuthorization\Facades;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection getConfig()
 * @method static string|null superRoleGuard()
 * @method static string|null superRoleName()
 * @method static Collection getGuards()
 * @method static int getPaginationCount()
 * @method static Collection getModuledPermissionsNames()
 * @method static Collection getModuledPermissions(bool $canAccess = false)
 * @method static Collection getGuardModuledPermissionsNames(string $guard)
 * @method static Collection getGuardModuledPermissions(string $guard,bool $canAccess = false)
 * @method static bool validCanAccess($canAccess)
 * @method static void seedSuperRole()
 * @method static void seedGuardModuledPermissions(string $guard)
 * @method static void seedMissingGuardsModuledPermissions()
 * @method static void seed(?string $guard = null)
 * @method static void linkRelation(string $table,string $onDelete = 'set null')
 * @method static void unLinkRelation(string $table)
 *
 * @see \ZaghloulSoft\LaravelAuthorization\Utilities\Role
 */
class Role extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Role';
    }
}
