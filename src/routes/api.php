<?php
use Illuminate\Support\Facades\Route;
//use ZaghloulSoft\LaravelAuthorization\Facades\Role;

Route::group([
    'prefix'=> 'api/roles'
],function (){
    Route::get('done',fn() => Role::availableModuledPermissions());
});
