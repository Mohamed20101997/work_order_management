<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'part_number' => 'P-'.fake()->unique()->numerify('#####'),
            'name' => ucfirst(fake()->randomElement(['bearing', 'seal kit', 'impeller', 'stator winding', 'fan blade', 'terminal box', 'shaft sleeve', 'gasket set'])).' '.fake()->numerify('T-##'),
            'quantity' => fake()->numberBetween(5, 80),
            'minimum_quantity' => 5,
            'unit' => 'pcs',
            'is_active' => true,
        ];
    }
}
