<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'PKG-' . fake()->unique()->lexify('??????'),
            'name' => 'Paket Umrah ' . fake()->monthName(),
            'slug' => fake()->unique()->slug(3),
            'type' => 'umrah',
            'category' => 'reguler',
            'departure_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'return_date' => fake()->dateTimeBetween('+7 months', '+8 months'),
            'duration_days' => 9,
            'price_single' => 35000000,
            'price_double' => 30000000,
            'price_triple' => 27000000,
            'price_quad' => 25000000,
            'quota' => 100,
            'booked_count' => 0,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }
}
