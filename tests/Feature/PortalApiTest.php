<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Cluster;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Pilgrim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_login_page_loads(): void
    {
        $response = $this->get('/portal/login');
        $response->assertStatus(200);
    }

    public function test_portal_login_requires_credentials(): void
    {
        $response = $this->post('/portal/login', []);
        $response->assertSessionHasErrors();
    }

    public function test_api_packages_featured(): void
    {
        Package::factory()->count(3)->create(['is_active' => true, 'is_featured' => true]);
        Package::factory()->count(2)->create(['is_active' => true, 'is_featured' => false]);

        $response = $this->getJson('/api/packages/featured');
        $response->assertStatus(200);
    }

    public function test_guest_can_view_sitemap(): void
    {
        $response = $this->get('/sitemap/sitemap.xml');
        $response->assertStatus(200);
        $this->assertStringContainsString('xml', $response->headers->get('Content-Type'));
    }

    public function test_guest_can_view_robots(): void
    {
        $response = $this->get('/robots.txt');
        $response->assertStatus(200);
    }

    public function test_payment_number_generation(): void
    {
        $number = Payment::generatePaymentNumber();
        $this->assertStringStartsWith('PMT-', $number);
    }

    public function test_booking_belongs_to_package(): void
    {
        $package = Package::factory()->create();
        $booking = Booking::factory()->create(['package_id' => $package->id]);

        $this->assertEquals($package->id, $booking->package->id);
    }

    public function test_booking_belongs_to_pilgrim(): void
    {
        $pilgrim = Pilgrim::factory()->create();
        $booking = Booking::factory()->create(['pilgrim_id' => $pilgrim->id]);

        $this->assertEquals($pilgrim->id, $booking->pilgrim->id);
    }

    public function test_invoice_belongs_to_booking(): void
    {
        $booking = Booking::factory()->create();
        $invoice = Invoice::factory()->create(['booking_id' => $booking->id]);

        $this->assertEquals($booking->id, $invoice->booking->id);
    }

    public function test_cluster_belongs_to_package(): void
    {
        $package = Package::factory()->create();
        $user = User::factory()->create(['role' => 'muthawif']);
        $cluster = Cluster::create([
            'code' => 'CL-003-TST',
            'name' => 'Test Kloter',
            'package_id' => $package->id,
            'muthawif_id' => $user->id,
            'capacity' => 40,
            'booked_count' => 0,
            'status' => 'scheduled',
        ]);

        $this->assertEquals($package->id, $cluster->package->id);
    }

    public function test_multiple_payments_on_single_invoice(): void
    {
        $booking = Booking::factory()->create(['total_price' => 30000000]);
        $invoice = Invoice::factory()->create([
            'booking_id' => $booking->id,
            'total_amount' => 30000000,
            'paid_amount' => 0,
            'remaining_amount' => 30000000,
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

        $invoice->refresh()->update([
            'paid_amount' => 10000000,
            'remaining_amount' => 20000000,
            'status' => 'partial',
        ]);

        Payment::create([
            'payment_number' => Payment::generatePaymentNumber(),
            'invoice_id' => $invoice->id,
            'booking_id' => $booking->id,
            'amount' => 20000000,
            'payment_method' => 'transfer',
            'payment_date' => now(),
            'status' => 'confirmed',
        ]);

        $invoice->refresh()->update([
            'paid_amount' => 30000000,
            'remaining_amount' => 0,
            'status' => 'paid',
        ]);

        $this->assertEquals('paid', $invoice->fresh()->status);
        $this->assertEquals(30000000, $invoice->fresh()->paid_amount);
    }

    public function test_pilgrim_mahram_relationship(): void
    {
        $mahram = Pilgrim::factory()->create(['gender' => 'male']);
        $pilgrim = Pilgrim::factory()->create([
            'gender' => 'female',
            'mahram_id' => $mahram->id,
        ]);

        $this->assertEquals($mahram->id, $pilgrim->mahram->id);
    }

    public function test_pilgrim_referral_relationship(): void
    {
        $referrer = Pilgrim::factory()->create();
        $referred = Pilgrim::factory()->create([
            'referral_pilgrim_id' => $referrer->id,
        ]);

        $this->assertEquals($referrer->id, $referred->referralPilgrim->id);
    }
}
