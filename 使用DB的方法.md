# 简介
laravel为我们查询和操作数据库提供了一套流接口,其使用PDO的参数绑定来避免SQL的注入攻击
# 获取数据

## 1. table&amp;get
table方法用于指定查询条件的数据表,而get用于获取所有符合条件的记录
`$users = DB::table('users')->get();`
## 2. first
只获取第一行的数据
`$user = DB::table('users')->first();`
## 3. value
只获取第一行的指定列数据,注意必须要指定需要获取到的类
`$name = DB::table('users')->value('name');`
## 4. pluck
获取符合条件的列数据,注意必须要指定需要获取到的列
`$names = DB::table('users')->pluck('name');`
## 5. chunk
chunk的作用是分块的去获取数据,每次仅获取指定个数的记录。通常在处理比较大的数据量的时候,我们会用到那chunk,举个例子:如果现在我们的数据库已经存在百万级的用户
数据,而因为一些不可抗力的因素,现在加密的密钥变更了,所以需要我们把密码字段全部变更,这个时候我们可以通过artisan命令去做这样的一个加密操作,但是因为数据量的
异常庞大,如果我们不使用类似chunk这样的获取数据方式,处理这么大的数据量的话,简直是恶梦。
```
DB::table('users')->chunk(5, function ($users) {
        foreach ($users as $user) {
//            dump($user);
        }
    });
```
## 6. select
只获取指定列的数据
`$users = DB::table('users')->select('name','email')->get();`
## 7. distinct
过滤掉重复的数据
`$users = DB::table('users')->select('name','email')->distinct()->get();`
## 7. 聚合函数
```
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
    //分块获取记录,每次获取指定条记录的数据
    $userNum = DB::table('users')->count();
    $maxId = DB::table('users')->max('id');
    $minId = DB::table('users')->min('id');
    $avgId = DB::table('users')->avg('id');
    $sumId = DB::table('users')->sum('id');
    //常用的聚合函数
});
```
# 条件约束
laravel的where语句最基本的调用方式需要传递三个参数,第一个参数是列名,第二个参数是数据库支持的操作符,而第三个参数是参与比较的值。如果第二个参数是=的话,那么
默认可以省略,只传递两个参数,一个参与比较的列,一个参与比较的值
例如,下面两个同样是只查询指定id的数据
$user = DB
# 结果约束
# 子查询
# join
# 增,删,改
# 琐
# 总结&amp;注意