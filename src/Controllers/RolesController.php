<?php
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpUndefinedMethodInspection */

namespace ZaghloulSoft\LaravelAuthorization\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use ZaghloulSoft\LaravelAuthorization\Facades\Role;
use ZaghloulSoft\LaravelAuthorization\Requests\AssignRoleRequest;
use ZaghloulSoft\LaravelAuthorization\Requests\CreateRoleRequest;
use ZaghloulSoft\LaravelAuthorization\Requests\IndexRolesRequest;
use ZaghloulSoft\LaravelAuthorization\Requests\UpdateRoleRequest;
use ZaghloulSoft\LaravelAuthorization\Services\AssignRoleService;
use ZaghloulSoft\LaravelAuthorization\Services\CreateRoleService;
use ZaghloulSoft\LaravelAuthorization\Services\DeleteRoleService;
use ZaghloulSoft\LaravelAuthorization\Services\indexRolesService;
use ZaghloulSoft\LaravelAuthorization\Services\ShowRoleService;
use ZaghloulSoft\LaravelAuthorization\Services\UpdateRoleService;
use ZaghloulSoft\LaravelAuthorization\Traits\Response;

class RolesController extends Controller
{
    use Response;

    /**
     * Display a listing of the resource.
     * @param IndexRolesRequest $request
     * @param indexRolesService $service
     * @return JsonResponse
     */
    public function index(IndexRolesRequest $request, IndexRolesService $service)
    {
        $roles = $service->execute($request);
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
     * @param CreateRoleService $service
     * @return JsonResponse
     */
    public function create(CreateRoleRequest $request, CreateRoleService $service)
    {
        $role = $service->execute($request);
        return $this->data(trans('laravel-authorization.create'),compact('role'));
    }

    /**
     * Display the specified resource.
     * @param
     * @param ShowRoleService $service
     * @return JsonResponse
     */
    public function show($id , ShowRoleService $service)
    {
        $role = $service->execute($id);
        return $this->data(trans('laravel-authorization.show'),compact('role'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRoleRequest $request
     * @param UpdateRoleService $service
     * @return JsonResponse
     */
    public function update(UpdateRoleRequest $request, UpdateRoleService $service)
    {
        $role = $service->execute($request);
        return $this->data(trans('laravel-authorization.update'), compact('role'));
    }

    /**
     * Assign Role to specific one.
     *
     * @param AssignRoleRequest $request
     * @param $id
     * @param AssignRoleService $service
     * @return JsonResponse
     */
    public function assign(AssignRoleRequest $request, $id, AssignRoleService $service)
    {
        $data = $service->execute($request,$id);
        return $this->data(trans('laravel-authorization.assign'), $data);
    }

    /**
     * Remove the specified resource from storage.
     * @param
     * @param DeleteRoleService $service
     * @return JsonResponse
     */
    public function delete($id, DeleteRoleService $service)
    {
        $delete = $service->execute($id);
        return $this->data(trans('laravel-authorization.delete'),compact('delete'));
    }
}
