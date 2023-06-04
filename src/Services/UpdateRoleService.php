<?php

namespace ZaghloulSoft\LaravelAuthorization\Services;

use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;
use ZaghloulSoft\LaravelAuthorization\Requests\UpdateRoleRequest;

class UpdateRoleService
{
    /**
     * @param UpdateRoleRequest $request
     * @return RoleModel|null
     */
    public function execute(UpdateRoleRequest $request): ?RoleModel
    {
        $request->role->update($request->validated());
        Role::seedMissingGuardsModuledPermissions();
        return $request->role;
    }
}
