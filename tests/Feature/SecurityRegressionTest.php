<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\PaymentProvider;
use App\Models\Pilgrim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function makePackage(): Package
    {
        return Package::create([
            'code' => 'TST-001', 'name' => 'Test', 'slug' => 'test-1',
            'type' => 'umrah', 'category' => 'reguler',
            'departure_date' => now()->addMonth(), 'return_date' => now()->addMonth()->addDays(9),
            'duration_days' => 9, 'price_quad' => 10000000, 'price_triple' => 11000000,
            'price_double' => 12000000, 'price_single' => 15000000,
            'quota' => 10, 'booked_count' => 0, 'available_count' => 10,
            'is_active' => true,
        ]);
    }

    public function test_callback_without_signature_rejected(): void
    {
        $pkg = $this->makePackage();
        $pilgrim = Pilgrim::factory()->create();
        $booking = \App\Models\Booking::create([
            'booking_number' => \App\Models\Booking::generateBookingNumberSafe(),
            'package_id' => $pkg->id, 'pilgrim_id' => $pilgrim->id, 'room_type' => 'quad',
            'package_price' => 10000000, 'total_price' => 10000000,
            'paid_amount' => 0, 'remaining_amount' => 10000000, 'status' => 'pending',
        ]);
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumberSafe(),
            'booking_id' => $booking->id, 'pilgrim_id' => $pilgrim->id,
            'total_amount' => 10000000, 'paid_amount' => 0, 'remaining_amount' => 10000000,
            'status' => 'unpaid', 'due_date' => now()->addDays(7),
        ]);
        PaymentProvider::create([
            'name' => 'Sim', 'code' => 'simulator', 'api_format' => 'simulator',
            'is_active' => true, 'is_default' => true, 'sort_order' => 1,
        ]);

        // Spoof tanpa signature wajib ditolak, invoice tetap unpaid
        $res = $this->postJson("/payment/{$invoice->id}/callback", [
            'order_id' => $invoice->invoice_number . '-123',
            'transaction_status' => 'settlement',
            'gross_amount' => '10000000',
        ]);
        $res->assertStatus(400);
        $this->assertEquals('unpaid', $invoice->fresh()->status);
    }

    public function test_simulator_blocked_in_production(): void
    {
        app()->detectEnvironment(fn () => 'production');
        $pkg = $this->makePackage();
        $pilgrim = Pilgrim::factory()->create();
        $booking = \App\Models\Booking::create([
            'booking_number' => \App\Models\Booking::generateBookingNumberSafe(),
            'package_id' => $pkg->id, 'pilgrim_id' => $pilgrim->id, 'room_type' => 'quad',
            'package_price' => 10000000, 'total_price' => 10000000,
            'paid_amount' => 0, 'remaining_amount' => 10000000, 'status' => 'pending',
        ]);
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumberSafe(),
            'booking_id' => $booking->id, 'pilgrim_id' => $pilgrim->id,
            'total_amount' => 10000000, 'paid_amount' => 0, 'remaining_amount' => 10000000,
            'status' => 'unpaid', 'due_date' => now()->addDays(7),
        ]);

        $this->get("/payment/{$invoice->id}/simulator")->assertStatus(404);
        // POST tanpa CSRF bisa 419 (blocked) atau 404 (blocked) — keduanya aman
        $postStatus = $this->post("/payment/{$invoice->id}/simulator-confirm")->getStatusCode();
        $this->assertTrue(in_array($postStatus, [404, 419]), "Expected 404/419, got {$postStatus}");
        $this->assertEquals('unpaid', $invoice->fresh()->status);
    }

    public function test_pilgrim_cannot_upload_payment_for_other_invoice(): void
    {
        $pkg = $this->makePackage();
        $a = Pilgrim::factory()->create();
        $b = Pilgrim::factory()->create();
        $mkBooking = fn ($p) => \App\Models\Booking::create([
            'booking_number' => \App\Models\Booking::generateBookingNumberSafe(),
            'package_id' => $pkg->id, 'pilgrim_id' => $p->id, 'room_type' => 'quad',
            'package_price' => 10000000, 'total_price' => 10000000,
            'paid_amount' => 0, 'remaining_amount' => 10000000, 'status' => 'pending',
        ]);
        $ba = $mkBooking($a);
        $bb = $mkBooking($b);
        $invB = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumberSafe(),
            'booking_id' => $bb->id, 'pilgrim_id' => $b->id,
            'total_amount' => 10000000, 'paid_amount' => 0, 'remaining_amount' => 10000000,
            'status' => 'unpaid', 'due_date' => now()->addDays(7),
        ]);

        $token = $a->createToken('pilgrim-app')->plainTextToken;
        $res = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson('/api/me/payments/upload', [
            'invoice_id' => $invB->id, 'booking_id' => $bb->id, 'amount' => 100000,
            'bank_name' => 'BSI', 'bank_account' => '123', 'bank_holder' => 'A',
            'payment_date' => now()->toDateString(),
        ]);
        $res->assertStatus(404);
    }

    public function test_agent_cannot_access_filament(): void
    {
        $agent = User::factory()->create(['role' => 'agent', 'is_active' => true]);
        $this->assertFalse($agent->canAccessFilament());
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->assertTrue($admin->canAccessFilament());
    }
}
