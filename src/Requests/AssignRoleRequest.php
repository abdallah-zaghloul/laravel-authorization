<?php
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpUndefinedFieldInspection */

namespace ZaghloulSoft\LaravelAuthorization\Requests;
use Illuminate\Validation\Rule;
use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;


class AssignRoleRequest extends MainRequestStub
{
    public ?RoleModel $role;
    public ?string $guardTable;
    public ?string $guardPrimaryKeyColumn;

    /**
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->role = $this->getRole();
        $this->guardTable = Role::getGuardTable($this->role->getAttributeValue('guard'));
        $this->guardPrimaryKeyColumn = Role::getGuardPrimaryKeyColumn($this->guardTable);
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
            'guardPrimaryKey'=> ['required',Rule::exists($this->guardTable ,$this->guardPrimaryKeyColumn)],
        ];
    }
}
