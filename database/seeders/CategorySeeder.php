<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [ 'title' => 'Politics', 'slug' => 'politics' ],
            [ 'title' => 'Sport', 'slug' => 'sport' ],
            [ 'title' => 'Economy', 'slug' => 'economy' ],
            [ 'title' => 'Technology', 'slug' => 'technology' ],
            [ 'title' => 'Future', 'slug' => 'future' ],
            [ 'title' => 'Show Business', 'slug' => 'show-business' ],
            [ 'title' => 'Society', 'slug' => 'society' ],
            [ 'title' => 'Nature', 'slug' => 'nature' ],
            [ 'title' => 'Other', 'slug' => 'other' ]
        ];

        DB::table('categories')->insert($categories);
    }
}
