<?php

namespace Database\Factories;

use App\Enums\AssetStatus;
use App\Enums\Priority;
use App\Models\AssetType;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asset_number' => 'AST-'.fake()->unique()->numerify('######'),
            'asset_type_id' => AssetType::factory(),
            'name' => fake()->randomElement(['Induction Motor', 'Centrifugal Pump', 'Diesel Generator', 'Air Compressor']).' '.fake()->numerify('##kW'),
            'serial_number' => strtoupper(fake()->unique()->bothify('SN########')),
            'manufacturer' => fake()->randomElement(['Siemens', 'ABB', 'WEG', 'Grundfos', 'Atlas Copco']),
            'model' => strtoupper(fake()->bothify('MDL-###')),
            'company_id' => Company::factory(),
            'location' => 'Bay '.fake()->numberBetween(1, 6),
            'received_date' => fake()->dateTimeBetween('-60 days', 'now'),
            'status' => AssetStatus::Received,
            'priority' => Priority::Normal,
        ];
    }
}
