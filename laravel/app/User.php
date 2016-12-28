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
    //白名单
    public $fillable = ['name','email','password'];
    //黑名单
//    public $guarded = ['updated_at','created_at','remember_token'];
}
