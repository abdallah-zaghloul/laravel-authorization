<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace ZaghloulSoft\LaravelAuthorization\Requests;
use Illuminate\Validation\Rule;
use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use ZaghloulSoft\LaravelAuthorization\Rules\ValidModuledPermissionsRule;


class CreateRoleRequest extends MainRequestStub
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=> [
                'required',
                Rule::unique(app(RoleModel::class)->getTable(),'name')
            ],
            'guard'=> [
                'required',
                Rule::in(Role::getGuards())
            ],
            'moduled_permissions' => [
                'required',
                app(ValidModuledPermissionsRule::class ,['guard'=> $this->input('guard')])
            ],
        ];
    }

}
