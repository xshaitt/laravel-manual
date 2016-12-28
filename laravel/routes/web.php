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
    $users = DB::table('users')->select('name', 'email')->get();
    //分块获取记录,每次获取指定条记录的数据
    $userNum = DB::table('users')->count();
    $maxId = DB::table('users')->max('id');
    $minId = DB::table('users')->min('id');
    $avgId = DB::table('users')->avg('id');
    $sumId = DB::table('users')->sum('id');
    dump($users);
});
//where
Route::get('where', function () {
    $user = DB::table('users')->where('id', '=', '15')->first();
    $user = DB::table('users')->where('id', '15')->first();
    //以上两个方法是等价的,如果where操作符为=的话,则第二个参数可以省略
    $users = DB::table('users')->where('name', 'like', '%a%')->get();
    //模糊查询
    $users = DB::table('users')->where([
        ['name', 'like', '%a%'],
        ['id', '>', '15'],
        ['id', '<', '25']
    ])->get();
    //使用数组的方式传递多个条件给where语句
    $user = DB::table('users')->where('id', '12')->orWhere('id', '15')->first();
    //多个where条件,where默认使用and拼接多个条件,如果为orWhere则使用or去拼接多个where条件
    $users = DB::table('users')->whereBetween('id', [2, 20])->get();
    //查询在指定区间的记录
    $users = DB::table('users')->whereNotBetween('id', [2, 20])->get();
    //查询不在指定区间的记录
    $users = DB::table('users')->whereIn('id', [1, 2, 3, 4, 5])->get();
    //查询在指定数组存在的记录
    $users = DB::table('users')->whereNotIn('id', [1, 2, 3, 4, 5])->get();
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
    $users = DB::table('users')->where('id', 15)
        ->orWhere(function ($query) {
            $query->where('name', 'like', '%a%')->orWhere('name', 'like', '%b%');
        })->get();
    //如果需要查询id为5或者name带有a或b的记录,那么这个时候就应该把id为5以及name带有a或者b分成两个组
    $users = DB::table('use1rs')->whereExists(function ($query) {
        $query->from('articles')->whereRaw('articles.user_id = users.id');
    })->toSQL();
    //而上面这样的语句与下面这个查询语句是等价的
    //select * from `use1rs` where exists (select * from `articles` where articles.user_id = users.id)
    //where exists子句用来方便编写where exists子句,如果我们需要查询发过文章的用户或者没有发过文章的用户,就应该使用whereExists子句
    //当然如果需要查询出没有发过文章的用户可以使用whereNotExists
    dump($users);
});
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
Route::get('articlenum', function () {
    //假使当前数据库有users表,articles表,部分的用户发表过文章,也就是说在articles表里有记录,我们需要查询出所有发表过文章的用户资料,以及他所发表的文章数量
    //并且发表文章的数量不是1篇,在这个环境下,仅使用laravel的内置方法如何去查询出来
    $users = DB::table('users')->join(DB::raw(
        '(' . DB::table('articles')->select(DB::raw('user_id,count(*) as num'))->groupBy('user_id')->toSQL() . ') article'
    ), 'users.id', '=', 'article.user_id')->select('users.id', 'users.name', 'article.num as article_num')
        ->having('article_num', '<>', 1)
        ->get();
    //上面的代码与下面的是等价的
    $users = DB::select('select `users`.`id`, `users`.`name`, `article`.`num` as `article_num` from `users` inner join (select user_id,count(*) as num from `articles` group by `user_id`) article on `users`.`id` = `article`.`user_id` having `article_num` <> 1');
    /**
     * 前者过于重度依赖laravel框架自身的方法,而后者感觉根本就没有发挥laravel的长处,下面是我心中相对比较优化的写法
     */
    $users = DB::table('users')->join(DB::raw(
        '(select user_id,count(*) as article_num from articles group by user_id having article_num <> 1) article'
    ), 'users.id', '=', 'article.user_id')->select('users.id', 'users.name', 'article.article_num')
        ->toSql();
    dump($users);
});
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
    /**
     * 使用排他锁的话,那么在当前事务就可以修改id为20的记录,但是因为最后事务没有提交,虽然sql返回了修改的行数但是最终数据也并没有修改
     */
    $result = DB::table('users')->where('id', 20)->first();
    dump($result);
});
Route::get('when',function(){
    $bool = false;
    $user = DB::table('users')->when($bool,function($query){
        return $query->orderBy('id','ASC')->first();
    },function($query){
        return $query->orderBy('id','DESC')->first();
    });
    dd($user);
});