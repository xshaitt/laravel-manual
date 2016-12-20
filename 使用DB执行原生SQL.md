# 使用DB执行原生SQL

`use Illuminate\Support\Facades\DB;`

先引入DB门面才可以使用DB关键字去操作数据库,关于门面会有专门的章节去进行讲解分析

在使用DB去处理数据库之前,推荐使用laravel提供的数据迁移去创建数据表,使用laravel提供的数据填充去创建数据,关于数据库的迁移与填充会在之后的章节里介绍到

连接指定数据库
`$users = DB::connection('mysql')->select('select * from users')`

创建一个users路由去查询users表所有数据routes/web.php
```
Route::get('users',function (){
    $users = DB::connection('mysql')->select('select * from users');
    return $users;
});
```
DB门面为几种类型的查询提供了方法:`select`,`update`,`insert`,`delete`,`statement`
## select
```
Route::get('select',function(){
    $users = DB::select('select * from users where id>?',[10]);
    //?的作用是占位符,随后在select的第二个参数里面以一个数组的形式提供值,预防sql注入,与pdo的操作方式类似
    return $users;
});
```
select返回的是查询到的结果
## update
```
Route::get('update',function(){
    $user = DB::update('update users set name = "xsh" where id = ?',[10]);
    return $user;
});
```
update返回的是影响到的行数
## insert
```
Route::get('insert', function () {
    $result = DB::insert('insert into users (name,email,password) values (:name,:email,:password)', ['name' => 'xshaitt', 'email' => 'xsh@gmail.com', 'password' => md5('123456')]);
    dd($result);
    //所以在这边就不能直接返回结果,否则就会有这样的报错The Response content must be a string or object implementing __toString(), "boolean" given.
});
```
insert方法返回的是执行结果的布尔值
## delete
```
Route::get('delete',function(){
    $nums = DB::delete('delete from users where id = ?',[33]);
    return $nums;
});
```
delete返回的是影响到的行数
## statement
```
Route::get('statement',function(){
    $result = DB::statement('drop table xxxx');
    dd($result);
});
```
insert方法返回的是执行结果的布尔值
# 总结:
> 通常如果仅仅是简单的增删改查或者一些并不太复杂的多表关联的话我们使用的比较多的还是eloquent,但有的时候一些比较复杂的sql语句还是会以这样的方式来执行的