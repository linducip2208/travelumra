<?php
namespace Database\Factories;
use App\Models\Booking;
use App\Models\Pilgrim;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
            'booking_id' => Booking::factory(),
            'pilgrim_id' => Pilgrim::factory(),
            'total_amount' => 25000000,
            'paid_amount' => 0,
            'remaining_amount' => 25000000,
            'status' => 'unpaid',
            'due_date' => now()->addDays(30),
        ];
    }
}
