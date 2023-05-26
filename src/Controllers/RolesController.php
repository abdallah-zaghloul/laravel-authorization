<?php
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpUndefinedMethodInspection */

namespace ZaghloulSoft\LaravelAuthorization\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ZaghloulSoft\LaravelAuthorization\Models\Role as RoleModel;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use ZaghloulSoft\LaravelAuthorization\Requests\CreateRoleRequest;
use ZaghloulSoft\LaravelAuthorization\Requests\IndexRolesRequest;
use ZaghloulSoft\LaravelAuthorization\Requests\UpdateRoleRequest;
use ZaghloulSoft\LaravelAuthorization\Traits\Response;

class RolesController extends Controller
{
    use Response;

    /**
     * Display a listing of the resource.
     *
     */
    public function index(IndexRolesRequest $request)
    {
        $roles = RoleModel::search($request->columns,$request->dates)->{$request->selectedFetchMethod['name']}(...$request->selectedFetchMethod['args']);
        return $this->data(trans('laravel-authorization.index'),compact('roles'));
    }


    /**
     * Display a listing of the resource.
     *
     */
    public function getModuledPermissions()
    {
        $moduledPermissions = Role::getModuledPermissions();
        return $this->data(trans('laravel-authorization.moduledPermissions'),compact('moduledPermissions'));
    }


    /**
     * Display a listing of the resource.
     *
     */
    public function getModuledPermissionsNames()
    {
        $moduledPermissionsNames = Role::getModuledPermissionsNames();
        return $this->data(trans('laravel-authorization.moduledPermissionsNames'),compact('moduledPermissionsNames'));
    }


    /**
     * Store a newly created resource in storage.
     * @param CreateRoleRequest $request
     * @return JsonResponse
     */

    public function create(CreateRoleRequest $request)
    {
        $role = RoleModel::create($request->validated());
        return $this->data(trans('laravel-authorization.create'),compact('role'));
    }

    /**
     * Display the specified resource.
     *
     */
    public function show($id)
    {
        $role = RoleModel::find($id);
        return $this->data(trans('laravel-authorization.show'),compact('role'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRoleRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        $request->role->update($request->validated());
        Role::seedMissingGuardsModuledPermissions();
        return $this->data(trans('laravel-authorization.update'), ['role'=> $request->role]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $delete = (bool) RoleModel::whereKey($id)->delete();
        Role::seedMissingGuardsModuledPermissions();
        return $this->data(trans('laravel-authorization.delete'),compact('delete'));
    }
}
