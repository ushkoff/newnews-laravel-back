<?php

namespace Database\Factories\News;

use App\Models\News\Article;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create();

        $title = $faker->sentence(rand(5, 20), true);
        $text = $faker->realText(rand(1000, 2500));
        $link = $faker->url;

        $country = $faker->country;
        $countryCode = $faker->countryCode;

        $pubkey = $faker->sha256;
        $signature = $faker->sha256;

        $ratingNumber = rand(-56, 1574);
        $isConfirmed = rand(1, 2) > 1;
        $cost = rand(10, 1000);
        $isEdited = rand(1, 2) > 1;

        $createdAt = $faker->date('Y-m-d H:i:s');

        $data = [
            'category_id' => rand(1, 9),
            'user_id' => (rand(1, 5) == 5) ? 1 : 2,
            'title' => $title,
            'content_html' => $text,
            'refs' => $link,
            'author_pubkey' => $pubkey,
            'signature' => $signature,
            'rating' => $ratingNumber,
            'is_confirmed' => $isConfirmed,
            'cost' => $cost,
            'is_edited' => $isEdited,
            'country' => $country,
            'country_code' => $countryCode,
            'created_at' => $createdAt
        ];

        return $data;
    }
}
