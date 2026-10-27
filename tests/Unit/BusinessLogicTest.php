<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Pilgrim;
use App\Models\Invoice;
use App\Services\RefundCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_number_generation(): void
    {
        $number = Booking::generateBookingNumber();
        $this->assertStringStartsWith('BKG-' . date('Ym'), $number);
    }

    public function test_invoice_number_generation(): void
    {
        $number = Invoice::generateInvoiceNumber();
        $this->assertStringStartsWith('INV-' . date('Ym'), $number);
    }

    public function test_promo_code_calculation_percentage(): void
    {
        $promo = new \App\Models\PromoCode([
            'code' => 'TEST10',
            'type' => 'percentage',
            'value' => 10,
            'max_discount' => 500000,
            'is_active' => true,
        ]);

        $discount = $promo->calculateDiscount(3000000);
        $this->assertEquals(300000, $discount);
    }

    public function test_promo_code_calculation_max_cap(): void
    {
        $promo = new \App\Models\PromoCode([
            'code' => 'TEST10',
            'type' => 'percentage',
            'value' => 10,
            'max_discount' => 500000,
            'is_active' => true,
        ]);

        $discount = $promo->calculateDiscount(10000000);
        $this->assertEquals(500000, $discount);
    }

    public function test_promo_code_fixed(): void
    {
        $promo = new \App\Models\PromoCode([
            'code' => 'FLAT500',
            'type' => 'fixed',
            'value' => 500000,
            'is_active' => true,
        ]);

        $discount = $promo->calculateDiscount(3000000);
        $this->assertEquals(500000, $discount);
    }

    public function test_refund_calculator_30_days(): void
    {
        $booking = Booking::factory()->make([
            'paid_amount' => 10000000,
        ]);
        $booking->setRelation('package', \App\Models\Package::factory()->make([
            'departure_date' => now()->addDays(35),
        ]));

        $calc = new RefundCalculator();
        $result = $calc->calculate($booking);

        $this->assertEquals(75, $result['refund_pct']);
        $this->assertEquals(7500000, $result['refund_amount']);
    }

    public function test_refund_calculator_7_days(): void
    {
        $booking = Booking::factory()->make([
            'paid_amount' => 10000000,
        ]);
        $booking->setRelation('package', \App\Models\Package::factory()->make([
            'departure_date' => now()->addDays(5),
        ]));

        $calc = new RefundCalculator();
        $result = $calc->calculate($booking);

        $this->assertEquals(0, $result['refund_pct']);
        $this->assertEquals(0, $result['refund_amount']);
    }

    public function test_pilgrim_registration_number_format(): void
    {
        $pilgrim = Pilgrim::factory()->create([
            'registration_number' => Pilgrim::generateRegistrationNumber(),
        ]);

        $this->assertStringStartsWith('JMA-', $pilgrim->registration_number);
    }

    public function test_phone_whatsapp_formatting(): void
    {
        $service = new \App\Services\WhatsAppService();
        $ref = new \ReflectionClass($service);
        $method = $ref->getMethod('formatPhone');
        $method->setAccessible(true);

        $this->assertEquals('6281234567890', $method->invoke($service, '081234567890'));
        $this->assertEquals('6281234567890', $method->invoke($service, '6281234567890'));
    }
}
