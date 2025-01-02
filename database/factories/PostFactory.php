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
        $type_option = ['normal', 'embed'];
        $thumbnail_option = "drone.jpg";

        return [
            'type' => $this->faker->randomElement($type_option),
            'title' => $this->faker->sentence(10),
            'description' => $this->faker->paragraph(5),
            'tag' => $this->faker->randomElement($tags_option),
            'rtime' => $this->faker->numberBetween(0,15),
            'content' => $this->faker->paragraph($nb = 100, $asText = true),
            'thumbnail' => $thumbnail_option,
            'post_at' => $this->faker->date('Y-m-d', 'now')
        ];
    }
}
