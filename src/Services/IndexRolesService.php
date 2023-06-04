<?php

namespace ZaghloulSoft\LaravelAuthorization\Services;

use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;
use ZaghloulSoft\LaravelAuthorization\Requests\IndexRolesRequest;

class IndexRolesService
{
    /**
     * @param IndexRolesRequest $request
     * @return mixed
     */
    public function execute(IndexRolesRequest $request)
    {
        return RoleModel::search($request->columns,$request->dates)->{$request->selectedFetchMethod['name']}(...$request->selectedFetchMethod['args']);
    }
}
