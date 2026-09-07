<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\WorkOrderStatus;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => 'WO-'.fake()->unique()->numerify('######'),
            'asset_id' => Asset::factory(),
            'reported_problem' => fake()->sentence(8),
            'status' => WorkOrderStatus::Open,
            'priority' => Priority::Normal,
            'created_by' => User::factory()->manager(),
        ];
    }
}
