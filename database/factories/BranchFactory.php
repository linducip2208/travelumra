<?php
namespace Database\Factories;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    public function definition(): array
    {
        $city = fake()->city();
        return [
            'name' => 'Cabang ' . $city,
            'code' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{2}'),
            'type' => 'branch',
            'city' => $city,
            'province' => fake()->state(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
            'quota_haji' => 50,
            'quota_umrah' => 150,
        ];
    }
}
