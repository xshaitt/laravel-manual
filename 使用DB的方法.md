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
在部分sql语句下,使用laravel的内置方法去创建可能并不太方便,但laravel提供了raw方法,用于局部注入原生的sql语句,比如说创建这样的一个查询:
查询出文章表的用户id和对应发表的文章数量,我们可以这样来写
`$article_num = DB::table('articles')->select('user_id','count(*) as article_num')->groupBy('user_id')->get();`
如果像上面那么去创建sql语句的话,那么一定会报错的,因为它生成的sql语句是这样的,laravel会自动的为count(*)加上\`\`,但我们表里面并没有count(*)这样的字段
```
select `user_id`, `count(*)` as `article_num` from `articles` group by `user_id`
```
那么在这样的情况下,我们使用raw注入一部分的参数更方便的点
`$article_num = DB::table('articles')->select('user_id',DB::raw('count(*) as article_num'))->groupBy('user_id')->get();`
上面代码最终生成的sql语句是这样的
```
select `user_id`, count(*) as article_num from `articles` group by `user_id`
```
# 结果约束
## 1.orderBy
使用orderBy对于结果集进行顺序或者倒序的排序
`$users = DB::table('users')->orderBy('id', 'DESC')->get();`
## 2.inRandomOrder
使用inRandomOrder可以打乱结果集，如果取一条数据的话，那么就相当于只每次查询获取不同数据
`$user = DB::table('users')->inRandomOrder()->first();`
## 3.groupBy
使用groupBy进行分组，会有去重的作用，但它是用来分组的
`$article_num = DB::table('articles')->groupBy('user_id')->get();`
## 4.having
where是产生结果集前的过滤，而having是产生结果集后的过滤，也就是说这是对于结果集过滤的方法
`$article_num = DB::table('articles')->select('user_id',DB::raw('count(*) as article_num'))->groupBy('user_id')->get();`
像上面的这个例子，因为article_num是通过起别名的方式来的，所以如果需要通过这个字段来过滤结果集的话，那么只能使用having了
## 5.havingRaw
加raw的基本都是表达使用原生表达式的值
`$article_num = DB::table('articles')->select(DB::raw('user_id,count(*) as article_num'))->groupBy('user_id')->havingRaw('article_num <> 1')->get();`
## 6.skip&amp;take与offset&amp;limit
限制结果集数量
```
$users = DB::table('users')->orderBy('id', 'DESC')->skip(5)->take(3)->get();
//从第5条记录开始,取3条记录
$users = DB::table('users')->orderBy('id', 'DESC')->offset(5)->limit(3)->get();
//从第5条开始,取3条记录
```
## 7.代码片段
```
Route::get('order', function () {
    $users = DB::table('users')->orderBy('id', 'DESC')->get();
    //对结果集排序
    $user = DB::table('users')->inRandomOrder()->first();
    //打乱结果集
    $article_num = DB::table('articles')->select(DB::raw('user_id,count(*) as article_num'))->groupBy('user_id')->get();
    /**
     * 分组,使用laravel内置的sql语句相关方法都会给字段和表名加上相对应的''与``但是如果count(*)被自动转化成`count(*)`的话就会找不到这个字段
     * 所以使用DB的raw就是不使用laravel去处理sql语句,而上面语句的作用就是查出文章表的每个用户的id及他们发表的文章数目
     */
    $article_num = DB::table('articles')->select('user_id',DB::raw('count(*) as article_num'))->groupBy('user_id')->get();
    //不查询发表1篇文章数的用户,因为articles表本身并没有article_num字段,而是通过重命名产生的字段,所有如果过滤值为1的情况使用where是会报错的,必须
    //使用having
    $article_num = DB::table('articles')->select(DB::raw('user_id,count(*) as article_num'))->groupBy('user_id')->havingRaw('article_num <> 1')->get();
    //havingRaw的作用是通过传递原生的表达式来过滤结果集
    $users = DB::table('users')->orderBy('id', 'DESC')->skip(5)->take(3)->get();
    //从第5条记录开始,取3条记录
    $users = DB::table('users')->orderBy('id', 'DESC')->offset(5)->limit(3)->get();
    //从第5条开始,取3条记录
    dump($article_num);
});
```
# 条件子句
有的时候我们需要根据条件的判断来执行两个不同的sql，那么laravel为我们提供了一个when方法

```php
<?php
Route::get('when',function(){
    $bool = false;
    $user = DB::table('users')->when($bool,function($query){
        return $query->orderBy('id','ASC')->first();
    },function($query){
        return $query->orderBy('id','DESC')->first();
    });
    dd($user);
});
```
使用when方法，第一个参数提供一个布尔值，第二三两个参数都是闭包方法，根据这个布尔值，如果为真则执行第二个参数，否则不执行，如果为假时需要返回别一个sql的话，则可以通过第三个参数来实现
# 多表连接
## 1.内连接
join
`user_article = DB::table('users')->join('articles', 'users.id', '=', 'articles.user_id')->select('users.id', 'articles.title')->get();`
## 2.左连接
leftJoin
`$user_article = DB::table('users')->leftJoin('articles', 'users.id', '=', 'articles.user_id')->select('users.id', 'articles.title')->get();`
## 3.交叉连接
所谓交叉就是求出两个表互相的所有可能，比如说：A是学生表，c是课程表，求出学生所有可能的选课情况
`$user_article = DB::table('users')->crossJoin('articles')->get();`
交叉连接,轻易不要使用,因为数据量异常的庞大
## 4.多条件连接
通过闭包的方式可以为连接设置多个条件
```php
<?php
$user_article = DB::table('users')->join('articles', function ($join) {
        $join->on('users.id', '=', 'articles.user_id')->where('users.id', '>', 20);
    })->select('users.id', 'articles.title')->get();
```
## 5.代码段
```php
<?php
Route::get('join', function () {
    //内连接
    $user_article = DB::table('users')->join('articles', 'users.id', '=', 'articles.user_id')->select('users.id', 'articles.title')->get();
    //左连接
    $user_article = DB::table('users')->leftJoin('articles', 'users.id', '=', 'articles.user_id')->select('users.id', 'articles.title')->get();
    //交叉连接,轻易不要使用,因为数据量异常的庞大,常用的情况:比如说有学生表和课程表需要查询出学生选课的所有可能
    $user_article = DB::table('users')->crossJoin('articles')->get();
    //高级连接语句,带多个on条件的连接
    $user_article = DB::table('users')->join('articles', function ($join) {
        $join->on('users.id', '=', 'articles.user_id')->on('users.id', '>', DB::raw(20));
        //上面语句第二个on条件的第3个参数因为laravel自动添加上``符号的原因所有这边必须强行注入原生语句
    })->select('users.id', 'articles.title')->get();
    //laravel也支持以where的形式给join添加多个连接条件
    $user_article = DB::table('users')->join('articles', function ($join) {
        $join->on('users.id', '=', 'articles.user_id')->where('users.id', '>', 20);
        //where语句没有这样的一个自动添加``符号的缘故所有不需要强行注入原生语句
    })->select('users.id', 'articles.title')->get();
    /**
     * 以上两条命令最终生成的查询语句为
     * select `users`.`id`, `articles`.`title` from `users` inner join `articles` on `users`.`id` = `articles`.`user_id` and `users`.`id` > ?
     * select `users`.`id`, `articles`.`title` from `users` inner join `articles` on `users`.`id` = `articles`.`user_id` and `users`.`id` > 20
     * 但因为一个使用了注入原生语句,所有就直接显示的值,没有了基本的防sql注入
     */
    dump($user_article);
});
```
# 增,删,改
## 1.插入1条数据
使用insert方法插入数据,需要提供一个数组参数，数组的每个元素就是对应要插入的值
`$user = DB::table('users')->insert(['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial.com','password'=>'lasdjfasldfjoewlalsdf']);`
## 2.插入多条数据
如果insert方法提供的参数是一个多维数据的话，那么就会插入多条数据
```php
<?php
$user = DB::table('users')->insert([
        ['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial1.com','password'=>'lasdjfasldfjoewlalsdf'],
        ['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial2.com','password'=>'lasdjfasldfjoewlalsdf'],
        ['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial3.com','password'=>'lasdjfasldfjoewlalsdf'],
        ['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial4.com','password'=>'lasdjfasldfjoewlalsdf']
    ]);
```
## 3.删除数据
使用delete方法删除记录，请一定谨记where条件的正确，否则对于数据库将是一场灭顶之灾，delete方法返回的是删除的行数
`$result = DB::table('logs')->where('id','20')->delete();`
## 4.清空整张表
当确实需要清空整张表的时候，我们可以使用`truncate`方法，它除了把表中所有的数据清除之外还会把di重置为1，这是一个非常危险的方法，一定要慎用
`$result = DB::table('users')->truncate();`
## 5.代码段
```php
<?
Route::get('dml', function () {
    //使用insert方法插入数据,插入单条数据,注意该方法返回的是,因为email字段有唯一索引,所有我在这里加了取随机值
//    $user = DB::table('users')->insert(['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial.com','password'=>'lasdjfasldfjoewlalsdf']);
    //同意插入多条数据
    /*$user = DB::table('users')->insert([
        ['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial1.com','password'=>'lasdjfasldfjoewlalsdf'],
        ['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial2.com','password'=>'lasdjfasldfjoewlalsdf'],
        ['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial3.com','password'=>'lasdjfasldfjoewlalsdf'],
        ['name'=>'xshaitt','email'=>time().mt_rand(5,15).'@gmial4.com','password'=>'lasdjfasldfjoewlalsdf']
    ]);*/
    //使用update方法更新数据,返回更新了多少行
//    $user = DB::table('users')->where('id',1)->update(['name'=>'first shuai']);
    //laravel甚至还集成对于数字型数据的增减,第二个参数为增减的值,默认为1
//    $result = DB::table('logs')->increment('see',1);
//    $result = DB::table('logs')->decrement('see');
    //使用delete方法删除数据,返回删除了多少行,注意一定要保证where语句没有问题,否则就是清表了
//    $result = DB::table('logs')->where('id','20')->delete();
    //清除整张表
//    $result = DB::table('users')->truncate();
//    dump($result);
});
```
# 锁
## 1.共享锁
如果在当前事务启用针对于表或者行启用了共享锁的话，那么任何事务都可以对于此表或者此行进行读取，同样的，任何事务对于此行或者此表都不能进行删除或者修改.锁的目的就是为了保证上锁的对象在事务执行期间数据的一致性，而共享锁的作用就是保证了它在事务执行期间任何读取的操作其结果都一致
```php
<?php
DB::beginTransaction();
$user = DB::table('users')->where('id', 20)->sharedLock()->get();
$result = DB::table('users')->where('id', 20)->update(['name' => '1234']);
dump($result);
```
因为事务最终没有进行提交所有当前的修改不会生效
## 2.排他锁
同样的，排他锁的目的就是保证了数据的完整性，只有加锁的事务才能对于数据进行操作
```php
<?php
DB::beginTransaction();
$user = DB::table('users')->where('id', 20)->lockForUpdate()->get();
```
验证代码是否正确只能通过多条事务来验证，所以在这边我就不做多余的操作了
## 3.代码段
```php
<?php
Route::get('suo', function () {
    //共享锁
//    DB::beginTransaction();
//    $user = DB::table('users')->where('id', 20)->sharedLock()->get();
    /**
     * 因为上面已经使用共享锁把id为20的行锁定了,所以在当前事务里任何针对于此行的修改语句都会执行失败
     * 在此事务之中因为加入了共享锁,所有在提交当前事务之前,所有的修改或者影响到id为20记录的都会执行失败
     * 如果不加共享锁的话,那么虽然说sql语句是可以正常的操作修改,并且也返回最后修改的行数,但是因为这个事务没有提交,最后也不会生效的
     */
//    $result = DB::table('users')->where('id', 20)->update(['name' => '1234']);
//    dump($result);
    //排他锁
    DB::beginTransaction();
    $user = DB::table('users')->where('id', 20)->lockForUpdate()->get();
});
```