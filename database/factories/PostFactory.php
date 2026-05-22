<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
        
            // sentence() 대신 realText()로 바꿔서 한글 단어 아무거나 생성.............
            'title' => $this->faker->realText(20), // 한글제발료
            'content' => $this->faker->realText(200),
        
            'view_count' => $this->faker->numberBetween(0, 100),
    ];
    }
}
