<?php /** @noinspection PhpUndefinedMethodInspection */

namespace ZaghloulSoft\LaravelAuthorization\Services;

use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;

class ShowRoleService
{
    /**
     * @param $id
     * @return mixed
     */
    public function execute($id)
    {
        return RoleModel::find($id);
    }
}
