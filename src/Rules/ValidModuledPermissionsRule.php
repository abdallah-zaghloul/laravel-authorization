<?php
/** @noinspection DuplicatedCode */
/** @noinspection PhpLanguageLevelInspection */

namespace ZaghloulSoft\LaravelAuthorization\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use ZaghloulSoft\LaravelAuthorization\Traits\Response;

class ValidModuledPermissionsRule implements Rule
{
    use Response;
    protected $attribute;
    protected $value;
    protected $module;
    protected $permissions;
    protected $permissionsValues;
    protected $invalidIndexType;
    protected Collection $defaults;
    protected string $guard;

    public function __construct(string $guard)
    {
        $this->defaults = Role::getGuardModuledPermissions($guard);
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value) : bool
    {
        $this->attribute = $attribute;
        $this->value = $this->validInput($value);

        $validModules = $this->value->filter(fn($permissions,$module) => $this->validModule($module,$permissions));
        return $validModules->count() === $this->defaults->count();
    }

    /**
     * @param $value
     * @return Collection|HttpResponseException
     */
    public function validInput($value)
    {
        $inputs = is_array($value) ? collect($value) : $this->invalid('invalidAttribute');
        collect([
            'extraModules' => $inputs->diffKeys($this->defaults)->keys(),
            'missingModules' => $this->defaults->diffKeys($inputs)->keys(),
        ])->each(function(Collection $invalids , $invalidIndexType) {
            $invalids->isNotEmpty() and $this->invalid($invalidIndexType, $invalids->implode(','));
        });
        return $inputs;
    }

    /**
     * @param $module
     * @param $permissions
     * @return bool
     */
    public function validModule($module, $permissions) : bool
    {
        [$modulePermissions , $defaultModulePermissions] = [collect($permissions) , collect($this->defaults->get($module))];

        $extraPermissions = $modulePermissions->diffKeys($defaultModulePermissions)->keys();
        $missingPermissions = $defaultModulePermissions->diffKeys($modulePermissions)->keys();
        $validPermissions = $modulePermissions->filter(fn($permissionValue,$permission) => Role::validCanAccess($permissionValue));
        $invalidPermissions = $modulePermissions->diffKeys($validPermissions);

        return $this->passedModule($module,$extraPermissions,$missingPermissions,$invalidPermissions);
    }

    /**
     * @param string $module
     * @param Collection $extraPermissions
     * @param Collection $missingPermissions
     * @param Collection $invalidPermissions
     * @return bool
     */
    public function passedModule(string $module, Collection $extraPermissions, Collection $missingPermissions, Collection $invalidPermissions): bool
    {
        collect([
            'extraPermissions'=> [
                'passed'=> $extraPermissions->isEmpty(),
                'args'=> ['extraPermissions' , $module , $extraPermissions->implode(',')]
            ],
            'missingPermissions'=> [
                'passed'=> $missingPermissions->isEmpty(),
                'args'=> ['missingPermissions' , $module , $missingPermissions->implode(',')]
            ],
            'invalidPermissions'=> [
                'passed'=> $invalidPermissions->isEmpty(),
                'args'=> ['invalidPermissions' , $module , $invalidPermissions->keys()->implode(',') , $invalidPermissions->values()->implode(',')]
            ],
        ])->where('passed',false)->whenNotEmpty(fn(Collection $invalids) => $this->invalid(...$invalids->first()['args']));

        return $extraPermissions->merge([...$missingPermissions,...$invalidPermissions->values()])->isEmpty();
    }

    /**
     * @param string $invalidIndexType
     * @param string|null $module
     * @param string|null $permissions
     * @param string|null $permissionsValues
     * @return HttpResponseException
     */
    public function invalid(string $invalidIndexType, ?string $module = null, ?string $permissions = null, ?string $permissionsValues = null): HttpResponseException
    {
        $this->invalidIndexType = $invalidIndexType;
        $this->module = $module;
        $this->permissions = $permissions;
        $this->permissionsValues = $permissionsValues;
        return $this->error($this->message(),$this->statusCodeBadRequest);
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        $messages = [
            'invalidAttribute'=> trans('laravel-authorization.invalidAttribute',[
                'attribute'=> $this->attribute,
            ]),
            'missingModules'=> trans('laravel-authorization.missingModules',[
                'attribute'=> $this->attribute,
                'module'=> $this->module
            ]),
            'extraModules'=> trans('laravel-authorization.extraModules',[
                'attribute'=> $this->attribute,
                'module'=> $this->module
            ]),
            'invalidPermissions'=> trans('laravel-authorization.invalidPermissions',[
                'attribute'=> $this->attribute,
                'module'=> $this->module,
                'permissions'=> $this->permissions,
                'permissionsValues'=> $this->permissionsValues,
            ]),
            'extraPermissions'=> trans('laravel-authorization.extraPermissions',[
                'attribute'=> $this->attribute,
                'module'=> $this->module,
                'permissions'=> $this->permissions
            ]),
            'missingPermissions'=> trans('laravel-authorization.missingPermissions',[
                'attribute'=> $this->attribute,
                'module'=> $this->module,
                'permissions'=> $this->permissions
            ]),
        ];

        return @$messages[$this->invalidIndexType] ?? $messages['invalidAttribute'];
    }

}
