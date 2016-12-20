<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});
$factory->define(App\Article::class, function (Faker\Generator $faker) {
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    return [
        'user_id' => mt_rand(1, 31),
        'title' => substr(str_shuffle($str), 0, mt_rand(5, 25)),
        'content' => substr(str_shuffle($str), 0, mt_rand(5, 25)),
    ];
});
$factory->define(App\Log::class, function (Faker\Generator $faker) {
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    return [
        'user_id' => mt_rand(1, 31),
        'title' => substr(str_shuffle($str), 0, mt_rand(5, 25)),
        'content' => substr(str_shuffle($str), 0, mt_rand(5, 25)),
    ];
});