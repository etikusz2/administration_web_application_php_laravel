<?php

namespace Database\Factories;

use App\Models\WebApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebApplicationFactory extends Factory
{
    protected $model = WebApplication::class;

    public function definition(): array
    {
        return [
            'webApplicationName' => $this->faker->unique()->word,
            'webApplicationType' => $this->faker->numberBetween(1, 5)
        ];
    }
}
