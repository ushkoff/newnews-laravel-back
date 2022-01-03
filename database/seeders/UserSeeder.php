<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email_verified_at = now();
        $createdAt = now();

        $data = [
            [
                'username'            => 'bumpzz',
                'email'               => 'bumpzz@example.com',
                'email_verified_at'   => $email_verified_at,
                'password'            => bcrypt('password'),
                'news_num'            => '76',
                'verified_news_num'   => '24',
                'country'             => 'United States Of America',
                'country_code'        => 'USA',
                'timezone'            => 'UTC+02:00',
                'news_confirm_notice' => 1,
                'created_at'          => $createdAt
            ],
            [
                'username'            => 'serapro',
                'email'               => 'serapro@example.com',
                'email_verified_at'   => $email_verified_at,
                'password'            => bcrypt('password'),
                'news_num'            => '84',
                'verified_news_num'   => '43',
                'country'             => 'Ukraine',
                'country_code'        => 'UA',
                'timezone'            => 'UTC+02:00',
                'news_confirm_notice' => 1,
                'created_at'          => $createdAt
            ],
            [
                'username'            => 'setik',
                'email'               => 'setik@example.com',
                'email_verified_at'   => $email_verified_at,
                'password'            => bcrypt('password'),
                'news_num'            => '23',
                'verified_news_num'   => '5',
                'country'             => 'Great Britain',
                'country_code'        => 'GB',
                'timezone'            => 'UTC+02:00',
                'news_confirm_notice' => 1,
                'created_at'          => $createdAt
            ]
        ];

        DB::table('users')->insert($data);
    }
}
