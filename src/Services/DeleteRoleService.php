<?php

namespace ZaghloulSoft\LaravelAuthorization\Services;

use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;

class DeleteRoleService
{

    /**
     * @param $id
     * @return bool
     */
    public function execute($id): bool
    {
        $delete = (bool) RoleModel::where('name','!=', Role::superRoleName())->whereKey($id)->delete();
        Role::seedMissingGuardsModuledPermissions();
        return $delete;
    }
}
