<?php

namespace Database\Seeders;

use Database\Seeders\Blog\PostSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        //Storage::makeDirectory('posts');

        $this->call([

        ]);

        $this->call(PostSeeder::class);
    }
}
