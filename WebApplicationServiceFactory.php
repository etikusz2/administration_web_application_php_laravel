<?php

namespace Database\Factories;

use App\Models\WebApplicationService;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebApplicationServiceFactory extends Factory
{
    protected $model = WebApplicationService::class;

    public function definition(): array
    {
        return [
            'webApplicationId' => \App\Models\WebApplication::factory(),
            'webServiceName' => $this->faker->unique()->companySuffix
        ];
    }
}
