<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
      //  factory(App\User::class, 20)->create();
//        factory(App\Category::class, 20)->create();
//        factory(App\Address::class, 20)->create();
        factory(App\Catalogue::class, 30)->create();

    }
}
