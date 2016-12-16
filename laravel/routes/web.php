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

Route::get('/', function () {
    return view('welcome');
});
Route::get('users', function () {
    $users = DB::connection('mysql')->select('select * from users');
    return $users;
});

Route::get('select', function () {
    $users = DB::select('select * from users where id>?', [10]);
    return $users;
});

Route::get('update', function () {
    $user = DB::update('update users set name = "xsh" where id = ?', [10]);
    return $user;
});

Route::get('insert', function () {
    $user = DB::insert('insert into users (name,email,password) values (:name,:email,:password)', ['name' => 'xshaitt', 'email' => 'xsh@gmail.com', 'password' => md5('123456')]);
    dd($user);
});

Route::get('delete',function(){
    $nums = DB::delete('delete from users where id = ?',[33]);
    return $nums;
});

Route::get('statement',function(){
    $result = DB::statement('drop table xxxx');
    dd($result);
});