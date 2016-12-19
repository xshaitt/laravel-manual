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

Route::get('delete', function () {
    $nums = DB::delete('delete from users where id = ?', [33]);
    return $nums;
});

Route::get('statement', function () {
    $result = DB::statement('drop table xxxx');
    dd($result);
});

///使用DB提供的数据库查询方法
Route::get('db', function () {
    $users = DB::table('users')->get();
    //table方法指定一个特定的数据表
    //返回的是一个laravel colection对象,存储有所有符合查询条件的对象
    $user = DB::table('users')->first();
    //返回的是第一个符合查询条件的记录,存储为数组的形式
    $name = DB::table('users')->value('name');
    //返回符合查询条件的第一条记录的指定列,必须指定列
    $names = DB::table('users')->pluck('name');
    //返回符合查询条件的所有记录的指定列,必须指定列
    DB::table('users')->chunk(5, function ($users) {
        foreach ($users as $user) {
//            dump($user);
        }
    });
    $users = DB::table('users')->select('name','email')->get();
    //分块获取记录,每次获取指定条记录的数据
    $userNum = DB::table('users')->count();
    $maxId = DB::table('users')->max('id');
    $minId = DB::table('users')->min('id');
    $avgId = DB::table('users')->avg('id');
    $sumId = DB::table('users')->sum('id');
    dump($users);
});
//where
Route::get('where',function(){
    $user = DB::table('users')->where('id','=','15')->first();
    $user = DB::table('users')->where('id','15')->first();
    //以上两个方法是等价的,如果where操作符为=的话,则第二个参数可以省略
    $users = DB::table('users')->where('name','like','%a%')->get();
    //模糊查询
    $users = DB::table('users')->where([
        ['name','like','%a%'],
        ['id','>','15'],
        ['id','<','25']
    ])->get();
    //使用数组的方式传递多个条件给where语句
    $user = DB::table('users')->where('id','12')->orWhere('id','15')->first();
    //多个where条件,where默认使用and拼接多个条件,如果为orWhere则使用or去拼接多个where条件
    $users = DB::table('users')->whereBetween('id', [2, 20])->get();
    //查询在指定区间的记录
    $users = DB::table('users')->whereNotBetween('id', [2, 20])->get();
    //查询不在指定区间的记录
    $users = DB::table('users')->whereIn('id', [1,2,3,4,5])->get();
    //查询在指定数组存在的记录
    $users = DB::table('users')->whereNotIn('id', [1,2,3,4,5])->get();
    //查询不在指定数组存在的记录
    $users = DB::table('users')->whereNull('updated_at')->get();
    //查询值为null的记录
    $users = DB::table('users')->whereNotNull('updated_at')->get();
    //查询值不为null的记录
    $users = DB::table('users')->whereDate('created_at', '2016-10-10')->get();
    //针对于时间的比较,年月日的比较
    $users = DB::table('users')->whereMonth('created_at', '10')->get();
    //针对于时间的比较,月的比较
    $users = DB::table('users')->whereDay('created_at', '10')->get();
    //针对于时间的比较,日的比较
    $users = DB::table('users')->whereYear('created_at', '2016')->get();
    //针对于时间的比较,年的比较
    $users = DB::table('users')->whereColumn('updated_at', 'created_at')->get();
    //两个字段之间的比较,两个参数默认为=
    $users = DB::table('users')->whereColumn('updated_at', '>', 'created_at')->get();
    //第二个参数指定操作符
    $users = DB::table('users')->whereColumn([
        ['id', '<>', 'updated_at'],
        ['updated_at', '=', 'created_at']
    ])->get();
    //whereColumn与where类似,可以传递数组指定同时指定多个条件
    $users = DB::table('users')->where('id',15)
        ->orWhere(function ($query){
            $query->where('name','like','%a%')->orWhere('name','like','%b%');
        })->get();
    //如果需要查询id为5或者name带有a或b的记录,那么这个时候就应该把id为5以及name带有a或者b分成两个组
    dump($users);
});