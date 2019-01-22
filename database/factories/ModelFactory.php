<?php
use Illuminate\Support\Facades\Hash;

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
    return [
        'name' => $faker->name,
        'mobile' => $faker->unique()->numberBetween(7000000000,9999999999),
        'password' => Hash::make('secret'),
        'category' => rand(1,2)
    ];
});

$factory->define(App\Category::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word,

    ];
});

$factory->define(App\Address::class, function (Faker\Generator $faker) {
    return [
        'storeName' => $faker->unique()->sentence(2),
        'area' => $faker->streetAddress,
        'city' => $faker->city,
        'longitude' => $faker->longitude() ,
        'latitude' => $faker->latitude()
    ];
});

$factory->define(App\Catalogue::class, function (Faker\Generator $faker) {
    $categoryIDs = \App\Category::all()->pluck('id')->toArray();
    $addressIDs = \App\Address::all()->pluck('id')->toArray();

    //comma separated random addresses with random count
    $count = rand(1,5);
    $addessAvailable = [];
    $start = $addressIDs[count($addressIDs) - $count-1];
    while ($count--) {
        $addessAvailable[] = $addressIDs[$start++];
    }


    return [
        'name' => $faker->unique()->sentence(2),
        'category_id' => $faker->randomElement($categoryIDs) ,
        'address' => implode(',' , $addessAvailable)
    ];
});
