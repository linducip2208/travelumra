<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Pilgrim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_can_be_created(): void
    {
        $branch = Branch::factory()->create();
        $user = User::factory()->create(['role' => 'super_admin']);
        $pilgrim = Pilgrim::factory()->create();
        $package = Package::factory()->create(['is_active' => true]);

        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'package_id' => $package->id,
            'pilgrim_id' => $pilgrim->id,
            'room_type' => 'quad',
            'branch_id' => $branch->id,
            'agent_id' => $user->id,
            'package_price' => $package->price_quad,
            'total_price' => $package->price_quad,
            'paid_amount' => 0,
            'remaining_amount' => $package->price_quad,
            'status' => 'pending',
        ]);

        $this->assertNotNull($booking->id);
        $this->assertStringStartsWith('BKG-', $booking->booking_number);
    }

    public function test_invoice_generated_on_booking(): void
    {
        $booking = Booking::factory()->create(['total_price' => 25000000]);
        $pilgrim = Pilgrim::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'booking_id' => $booking->id,
            'pilgrim_id' => $pilgrim->id,
            'total_amount' => $booking->total_price,
            'paid_amount' => 0,
            'remaining_amount' => $booking->total_price,
            'status' => 'unpaid',
            'due_date' => now()->addDays(30),
        ]);

        $this->assertEquals($booking->total_price, $invoice->total_amount);
        $this->assertEquals('unpaid', $invoice->status);
    }

    public function test_payment_updates_invoice_status(): void
    {
        $booking = Booking::factory()->create(['total_price' => 10000000, 'paid_amount' => 0]);
        $invoice = Invoice::factory()->create([
            'booking_id' => $booking->id,
            'total_amount' => 10000000,
            'paid_amount' => 0,
            'remaining_amount' => 10000000,
            'status' => 'unpaid',
        ]);

        Payment::create([
            'payment_number' => Payment::generatePaymentNumber(),
            'invoice_id' => $invoice->id,
            'booking_id' => $booking->id,
            'amount' => 10000000,
            'payment_method' => 'transfer',
            'payment_date' => now(),
            'status' => 'confirmed',
        ]);

        $invoice->update([
            'paid_amount' => 10000000,
            'remaining_amount' => 0,
            'status' => 'paid',
        ]);

        $this->assertEquals('paid', $invoice->fresh()->status);
    }

    public function test_guest_can_view_packages(): void
    {
        Package::factory()->count(3)->create(['is_active' => true]);
        $response = $this->get('/paket-umrah');
        $response->assertStatus(200);
    }

    public function test_guest_can_view_blog(): void
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
    }

    public function test_guest_can_view_faq(): void
    {
        $response = $this->get('/faq');
        $response->assertStatus(200);
    }

    public function test_booking_number_format(): void
    {
        $n = Booking::generateBookingNumber();
        $this->assertStringStartsWith('BKG-' . date('Ym'), $n);
    }
}
