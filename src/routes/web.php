<?php
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=> 'api/roles'
],function (){
    Route::get('done',fn() => 'done');
});

