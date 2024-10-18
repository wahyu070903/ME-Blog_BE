<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    public function definition(): array
    {
        $tags_option = ['TECHNOLOGY', 'ELECTRONICS','MECHANICAL','COMPUTER ENG'];
        $thumbnail_option = "drone.jpg";

        return [
            'title' => $this->faker->text(50),
            'description' => $this->faker->text(100),
            'tag' => $this->faker->randomElement($tags_option),
            'rtime' => $this->faker->numberBetween(0,15),
            'content' => $this->faker->text(2000),
            'thumbnail' => $thumbnail_option
        ];
    }
}
