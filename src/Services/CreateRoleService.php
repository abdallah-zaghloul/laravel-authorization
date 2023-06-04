<?php

namespace ZaghloulSoft\LaravelAuthorization\Services;

use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;
use ZaghloulSoft\LaravelAuthorization\Requests\CreateRoleRequest;

class CreateRoleService
{
    /**
     * @param CreateRoleRequest $request
     * @return mixed
     */
    public function execute(CreateRoleRequest $request)
    {
        return RoleModel::create($request->validated());
    }
}
