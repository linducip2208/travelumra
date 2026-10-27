<?php
namespace Database\Factories;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PilgrimFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_number' => \App\Models\Pilgrim::generateRegistrationNumber(),
            'full_name' => fake()->name(),
            'nik' => fake()->numerify('3174############'),
            'passport_number' => fake()->bothify('C#######'),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date(),
            'gender' => 'male',
            'phone' => fake()->phoneNumber(),
            'branch_id' => Branch::factory(),
            'is_active' => true,
        ];
    }
}
