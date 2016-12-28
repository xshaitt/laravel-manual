<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
//使用DB执行原生SQL

Route::get('/', function () {
    return view('welcome');
});
Route::get('eloquent',function(){
//    $users = App\User::all()->toArray();
//    dd($users);
    $users = App\User::where('id','>','10')->orderBy('id','DESC')->get();
    dd($users);
});
Route::get('find',function(){
//    $user = App\User::find(1);
//    dd($user);
    $users = App\User::find([1,2,3,4,5]);
    dd($users);
});
Route::get('findor',function(){
    $user = App\User::findOrFail(0);
    dd($user);
});