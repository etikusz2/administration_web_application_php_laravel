<?php

namespace Database\Factories;

use App\Models\AdministrativeUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdministrativeUnitFactory extends Factory
{
    protected $model = AdministrativeUnit::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
        ];
    }
}
