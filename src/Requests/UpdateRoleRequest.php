<?php
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpUndefinedFieldInspection */

namespace ZaghloulSoft\LaravelAuthorization\Requests;
use Illuminate\Validation\Rule;
use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use ZaghloulSoft\LaravelAuthorization\Rules\ValidModuledPermissionsRule;


class UpdateRoleRequest extends MainRequestStub
{
    public ?RoleModel $role;

    /**
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->role = $this->getRole();
        $this->mergeIfMissing(['guard'=> $this->role->getAttributeValue('guard')]);
    }

    /**
     * @return mixed
     */
    protected function getRole()
    {
        return RoleModel::findOrFail($this->route('id'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'guard'=> [
                'required',
                Rule::in(Role::getGuards())
            ],
            'name'=> [
                Rule::requiredIf(empty($this->input('moduled_permissions'))) ,
                Rule::unique($this->role->getTable(),'name')->ignore($this->route('id'))
            ],
            'moduled_permissions' => [
                Rule::requiredIf($this->input('guard') !== $this->role->getAttributeValue('guard')) ,
                app(ValidModuledPermissionsRule::class,['guard'=> $this->input('guard')])
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->role->getAttributeValue('name') === Role::superRoleName() and $this->error(trans('laravel-authorization.immutableSuperRole'), $this->statusCodeBadRequest);
    }

}
