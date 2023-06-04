<?php

namespace ZaghloulSoft\LaravelAuthorization\Services;

use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use ZaghloulSoft\LaravelAuthorization\Requests\AssignRoleRequest;
use ZaghloulSoft\LaravelAuthorization\Traits\Response;

class AssignRoleService
{
    use Response;

    /**
     * @param AssignRoleRequest $request
     * @param $id
     * @return array
     */
    public function execute(AssignRoleRequest $request, $id): array
    {
        $assign = Role::assign($request->guardTable,$request->guardPrimaryKeyColumn,$request->input('guardPrimaryKey'),$id);
        ! $assign and $this->error(trans('notAssigned'),$this->statusCodeBadRequest);
        return ['role'=> $request->role,'assign'=> $assign];
    }
}
