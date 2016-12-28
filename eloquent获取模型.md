# 获取模型
## 1.简介
你可以把eloquent看成一个功能更加强大的查询构建器，但与使用DB门面不同的是，在使用eloquent获取某个数据表的数据之前，
需要创建与之对应的模型，然后，尽情享受eloquent带来的便利吧。
## 2.获取所有模型
使用all方法获取所有模型，返回的是包含所有模型的一个Collection集合，关于这个集合，到时候会有单独的篇章介绍
```php
<?php
Route::get('eloquent',function(){
    $users = App\User::all()->toArray();
    //如果不需要获取完整的模型的话，那么可以使用toArray方法转换成数据，则只获取数据库里的数据
    dd($users);
});
```
## 3.获取符合条件的模型
上面有提到过eloquent对比DB其实就是一个功能更加强大的查询构建器，基于DB门面的方法，eloquent都可以使用，使用where过滤数据，
使用get获取符合条件的数据，使用orderBy排序等等
```php
<?php
Route::get('eloquent',function(){
    $users = App\User::where('id','>','10')->orderBy('id','DESC')->get();
    dd($users);
});
```
## 4.使用主键获取模型
如果通过主键去获取模型，那么laravel提供了一种更为方便的方法--find
### 1.获取单个模型
如果获取单个模型，那么find方法返回的就是对应的模型
```php
<?php
Route::get('find',function(){
    $user = App\User::find(1);
    dd($user);
});
```
### 2.获取多个模型
如果使用find获取的是多个模型，那么find返回的是包含多个模型的Collection集合
```php
<?php
Route::get('find',function(){
    $users = App\User::find([1,2,3,4,5]);
    dd($users);
});
```
## 5.自动抛出异常
有的时候我们我们希望如果查询不到记录的时候抛出异常，比如：更新用户资料的时候，我们势必会先查询出这个用户的记录，然后再根据需要来更改对应的字段，
如果查询不到对应的用户记录的话，抛出一个异常，捕获异常提示用户。鉴于此，laravel提供了findOrfail方法
```php
<?php
Route::get('findor',function(){
    //当如果查询到数据的时候，findOrFail与find是等价的，而如果查询不到记录的时候，findorFail就会抛出一个异常
    $user = App\User::findOrFail(0);
    dd($user);
});
```