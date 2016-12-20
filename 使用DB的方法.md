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
默认可以省略,只传递两个参数,一个参与比较的列,一个参与比较的值.
下面的这段代码对于where众多的使用方式有比较详细的
```
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
    $users = DB::table('use1rs')->whereExists(function ($query){
        $query->from('articles')->whereRaw('articles.user_id = users.id');
    })->toSQL();
    //而上面这样的语句与下面这个查询语句是等价的
    //select * from `use1rs` where exists (select * from `articles` where articles.user_id = users.id)
    //where exists子句用来方便编写where exists子句,如果我们需要查询发过文章的用户或者没有发过文章的用户,就应该使用whereExists子句
    //当然如果需要查询出没有发过文章的用户可以使用whereNotExists
    dump($users);
});
```
# 注入原生表达式
# 结果约束
## 1.orderBy
## 2.inRandomOrder
## 3.groupBy
## 4.having
## 5.havingRaw
## 6.skip&amp;take
# 子查询
# join
# 增,删,改
# 琐
# 总结&amp;注意