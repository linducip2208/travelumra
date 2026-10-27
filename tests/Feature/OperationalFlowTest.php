<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Branch;
use App\Models\Cluster;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Models\Lead;
use App\Models\LoyaltyPoint;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Pilgrim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cluster_can_be_created(): void
    {
        $package = Package::factory()->create();
        $user = User::factory()->create(['role' => 'muthawif']);

        $cluster = Cluster::create([
            'code' => 'CL-001-RAM',
            'name' => 'Kloter Ramadhan 1',
            'package_id' => $package->id,
            'muthawif_id' => $user->id,
            'capacity' => 40,
            'booked_count' => 0,
            'status' => 'scheduled',
        ]);

        $this->assertNotNull($cluster->id);
        $this->assertEquals('scheduled', $cluster->status);
    }

    public function test_pilgrim_can_be_assigned_to_cluster(): void
    {
        $package = Package::factory()->create();
        $pilgrim = Pilgrim::factory()->create();
        $user = User::factory()->create(['role' => 'super_admin']);
        $branch = Branch::factory()->create();
        $cluster = Cluster::create([
            'code' => 'CL-002-SHA',
            'name' => 'Kloter Syawal 1',
            'package_id' => $package->id,
            'muthawif_id' => $user->id,
            'capacity' => 40,
            'booked_count' => 0,
            'status' => 'scheduled',
        ]);

        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'package_id' => $package->id,
            'pilgrim_id' => $pilgrim->id,
            'room_type' => 'quad',
            'branch_id' => $branch->id,
            'agent_id' => $user->id,
            'package_price' => 25000000,
            'total_price' => 25000000,
            'paid_amount' => 0,
            'remaining_amount' => 25000000,
            'status' => 'pending',
        ]);

        $cluster->bookings()->attach($booking->id, [
            'seat_number' => '12A',
            'room_number' => '501-MKK',
            'catering_group' => 'A',
        ]);

        $this->assertCount(1, $cluster->bookings);
        $this->assertEquals('12A', $cluster->bookings->first()->pivot->seat_number);
    }

    public function test_insurance_can_be_created(): void
    {
        $booking = Booking::factory()->create();

        $insurance = Insurance::create([
            'booking_id' => $booking->id,
            'policy_number' => 'INS-2026-001-' . uniqid(),
            'coverage_type' => 'comprehensive',
            'provider' => 'ACA',
            'coverage_amount' => 100000000,
            'premium' => 250000,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'active',
        ]);

        $this->assertNotNull($insurance->id);
        $this->assertEquals('active', $insurance->status);
    }

    public function test_insurance_claim_can_be_filed(): void
    {
        $booking = Booking::factory()->create();
        $insurance = Insurance::create([
            'booking_id' => $booking->id,
            'policy_number' => 'INS-2026-002-' . uniqid(),
            'coverage_type' => 'comprehensive',
            'provider' => 'ACA',
            'coverage_amount' => 100000000,
            'premium' => 250000,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'active',
        ]);

        $claim = InsuranceClaim::create([
            'insurance_id' => $insurance->id,
            'claim_number' => 'CLM-' . uniqid(),
            'incident_type' => 'medical',
            'incident_date' => now(),
            'claim_amount' => 5000000,
            'description' => 'Rawat inap karena demam',
            'status' => 'submitted',
        ]);

        $this->assertNotNull($claim->id);
        $this->assertEquals('submitted', $claim->status);
        $this->assertEquals(5000000, $claim->claim_amount);
    }

    public function test_expense_can_be_recorded(): void
    {
        $branch = Branch::factory()->create();
        $user = User::factory()->create(['role' => 'super_admin']);
        $category = \App\Models\ExpenseCategory::create([
            'name' => 'Transportasi',
            'slug' => 'transportasi',
        ]);

        $expense = Expense::create([
            'expense_number' => 'EXP-001',
            'expense_category_id' => $category->id,
            'branch_id' => $branch->id,
            'created_by' => $user->id,
            'amount' => 1500000,
            'expense_date' => now(),
            'description' => 'Sewa bus antar jemput',
            'status' => 'pending',
        ]);

        $this->assertNotNull($expense->id);
        $this->assertEquals(1500000, $expense->amount);
        $this->assertEquals('pending', $expense->status);
    }

    public function test_lead_can_be_created(): void
    {
        $package = Package::factory()->create();

        $lead = Lead::create([
            'name' => 'Ahmad Fauzi',
            'phone' => '081234567890',
            'email' => 'ahmad@example.com',
            'source' => 'website',
            'interested_package_id' => $package->id,
            'status' => 'new',
            'notes' => 'Tertarik umrah Ramadhan',
        ]);

        $this->assertNotNull($lead->id);
        $this->assertEquals('new', $lead->status);
    }

    public function test_lead_can_be_assigned_to_agent(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $package = Package::factory()->create();
        $lead = Lead::create([
            'name' => 'Test Lead',
            'phone' => '081111111111',
            'source' => 'website',
            'interested_package_id' => $package->id,
            'status' => 'new',
        ]);

        $lead->update([
            'assigned_to' => $agent->id,
            'status' => 'contacted',
        ]);

        $this->assertEquals('contacted', $lead->fresh()->status);
        $this->assertEquals($agent->id, $lead->fresh()->assigned_to);
    }

    public function test_loyalty_points_awarded_on_completed_booking(): void
    {
        $pilgrim = Pilgrim::factory()->create();
        $booking = Booking::factory()->create([
            'pilgrim_id' => $pilgrim->id,
            'total_price' => 25000000,
            'paid_amount' => 25000000,
            'remaining_amount' => 0,
            'status' => 'returned',
        ]);

        $points = LoyaltyPoint::create([
            'pilgrim_id' => $pilgrim->id,
            'booking_id' => $booking->id,
            'points' => 25,
            'transaction_type' => 'earned',
            'description' => 'Booking completed: 25M IDR = 25 points',
        ]);

        $this->assertEquals(25, $points->points);
        $this->assertEquals('earned', $points->transaction_type);
    }

    public function test_loyalty_points_can_be_redeemed(): void
    {
        $pilgrim = Pilgrim::factory()->create();

        LoyaltyPoint::create([
            'pilgrim_id' => $pilgrim->id,
            'points' => 50,
            'transaction_type' => 'earned',
            'description' => 'Previous bookings',
        ]);

        $redemption = LoyaltyPoint::create([
            'pilgrim_id' => $pilgrim->id,
            'points' => -20,
            'transaction_type' => 'redeemed',
            'description' => 'Redeem 20 pts = 1.000.000 discount',
        ]);

        $this->assertEquals(-20, $redemption->points);
        $this->assertEquals('redeemed', $redemption->transaction_type);
    }

    public function test_payment_status_verified(): void
    {
        $booking = Booking::factory()->create(['total_price' => 10000000]);
        $invoice = \App\Models\Invoice::factory()->create([
            'booking_id' => $booking->id,
            'total_amount' => 10000000,
        ]);

        $payment = Payment::create([
            'payment_number' => Payment::generatePaymentNumber(),
            'invoice_id' => $invoice->id,
            'booking_id' => $booking->id,
            'amount' => 5000000,
            'payment_method' => 'transfer',
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $payment->status);

        $payment->update(['status' => 'confirmed']);
        $this->assertEquals('confirmed', $payment->fresh()->status);
    }

    public function test_booking_status_workflow_progresses(): void
    {
        $booking = Booking::factory()->create(['status' => 'pending']);

        $stages = ['dp_paid', 'confirmed', 'documents_complete', 'visa_approved', 'ready_to_depart'];

        foreach ($stages as $stage) {
            $booking->update(['status' => $stage]);
            $this->assertEquals($stage, $booking->fresh()->status);
        }
    }

    public function test_public_api_returns_packages(): void
    {
        Package::factory()->count(5)->create(['is_active' => true]);

        $response = $this->getJson('/api/packages');
        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(5, count($response->json('data')));
    }

    public function test_public_api_returns_single_package(): void
    {
        $package = Package::factory()->create(['is_active' => true]);

        $response = $this->getJson('/api/packages/' . $package->slug);
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', $package->name);
    }

    public function test_public_api_can_submit_lead(): void
    {
        $package = Package::factory()->create();

        $response = $this->postJson('/api/leads', [
            'name' => 'Budi Santoso',
            'phone' => '081298765432',
            'email' => 'budi@example.com',
            'source' => 'website',
            'interested_package_id' => $package->id,
            'notes' => 'Ingin info lebih lanjut',
        ]);

        $response->assertStatus(201);
    }

    public function test_guest_can_view_homepage(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_guest_can_view_about(): void
    {
        $response = $this->get('/tentang-kami');
        $response->assertStatus(200);
    }

    public function test_guest_can_view_contact(): void
    {
        $response = $this->get('/kontak');
        $response->assertStatus(200);
    }
}
