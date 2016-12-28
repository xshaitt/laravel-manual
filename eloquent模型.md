# Eloquent模型
## 1.简介
Eloquent是laravel框架提供的一种orm技术，通过调用一些简单的方法来完成一些相对复杂的操作，每张表都会对应一个模型，
我们可以调用这个模型的方法来完成增，删，改，查的操作。而每个模型其实都相当于一个查询构建器，所有对于在db中可以使用的方法，
在Eloquent模型当中也同样可以使用。
## 2.创建模型
除了手动创建以外，通过我们可以使用artisan命令来创建：

`php artisan make:model User`

该命令会在当前项目的app目录下面创建一个User.php文件，并且继承model类，文件内的代码如下：
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
}

```
如果为`make:model`带上-m或者--migration选项的话，那么则在创建model类的同时也会创建一个migration，
关于migration的介绍，我另有章节细说。

`php artisan make:model -m User`
## 3.常用属性
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //指定表名，laravel默认model连接的表名为model文件名小写并且加上s，本例子User.php模型默认对应的表名则为users
    public $table = 'users';
    //指定主键名，laravel对于所有表的主键都默认为id，如果不是，请通过$primaryKey属性指定
    public $primaryKey = 'id';
    //关闭laravel自动管理的列，如果使用laravel提供的create或者update方法去插入更新数据的话，那么默认laravel会往created_at和updated_at字段里插入当前的时间，如果不需要这样的功能，则通过给这个字段设置false关闭
    public $timestamps = false;
    //laravel自动管理的两个列默认的格式是：Y-m-d H:i:s，可以通过$dateFormat属性自动指定
    public $dateFormat = 'Y-m-d';
    //使用$connection属性指定当前model使用的数据库连接，前提是在config/database.php文件里配置过相对应的连接，默认使用mysql
    public $connection = 'mysql';
}

```