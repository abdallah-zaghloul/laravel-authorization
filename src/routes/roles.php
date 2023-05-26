<?php
use Illuminate\Support\Facades\Route;
use ZaghloulSoft\LaravelAuthorization\Controllers\RolesController;

Route::group([
    'prefix'=> 'api/roles',
//    'middleware'=> 'laravel.authorization:guard,module,permission' you can use this middleware at any route to apply authorization
],function (){
    Route::get('/',[RolesController::class,'index']);
    Route::post('create',[RolesController::class,'create']);
    Route::get('show/{id}',[RolesController::class,'show']);
    Route::get('delete/{id}',[RolesController::class,'delete']);
    Route::post('update/{id}',[RolesController::class,'update']);
    Route::post('assign/{id}',[RolesController::class,'assign']);
    Route::get('moduled-permissions',[RolesController::class,'getModuledPermissions']);
    Route::get('moduled-permissions-names',[RolesController::class,'getModuledPermissionsNames']);
});
