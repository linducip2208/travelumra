<?php
namespace Database\Factories;
use App\Models\Branch;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_number' => \App\Models\Booking::generateBookingNumber(),
            'package_id' => Package::factory(),
            'pilgrim_id' => Pilgrim::factory(),
            'room_type' => 'quad',
            'branch_id' => Branch::factory(),
            'agent_id' => User::factory(),
            'package_price' => 25000000,
            'total_price' => 25000000,
            'paid_amount' => 0,
            'remaining_amount' => 25000000,
            'status' => 'pending',
        ];
    }
}
