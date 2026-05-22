<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 려원님의 DB 설계도에 있는 컬럼명과 똑같아야 합니다!
            'category_name' => $this->faker->word(), 
        ];
    }
}