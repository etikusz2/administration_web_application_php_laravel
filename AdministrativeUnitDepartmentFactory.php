<?php

namespace Database\Factories;

use App\Models\AdministrativeUnitDepartment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdministrativeUnitDepartmentFactory extends Factory
{
    protected $model = AdministrativeUnitDepartment::class;

    public function definition(): array
    {
        return [
            'administrativeUnitId' => \App\Models\AdministrativeUnit::factory(),
            'departmentName' => $this->faker->unique()->word,
            'webApplicationId' => \App\Models\WebApplication::factory(),
            'webApplicationURL' => $this->faker->url
        ];
    }
}
