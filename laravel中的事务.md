# laravel中的事务
想要在laravel当中使用事务管理一系列的操作，可以使用两种方式，一种是DB门面的transaction方法自动管理事务，一种是DB门面的beginTransaction方法手动控制事务
## 1.自动管理事务
使用transaction方法需要提供一个闭包方法，在闭包方法内进行数据库操作，如果在闭包方法执行过程当中有异常抛，则进行回滚，闭包执行成功则提交事务
```php
<?php
DB::transaction(function () {
    DB::table('users')->update(['votes' => 1]);
    DB::table('posts')->delete();
});
```
## 2.手动使用事务
```php
<?php
DB::beginTransaction();
//开启事务
DB::rollBack();
//事务回滚
DB::commit();
//提交事务
```
## 注意
> 就算是在DB门面开启的事务，对于eloquent同样生效