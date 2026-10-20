<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Middleware\NormalizeRupiahInputs;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeRupiahInputsTest extends TestCase
{
    protected NormalizeRupiahInputs $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new NormalizeRupiahInputs();
    }

    public function test_normalizes_standard_rupiah_thousand_separators(): void
    {
        $this->assertSame(1000, $this->middleware->normalizeRupiahValue('1.000'));
        $this->assertSame(100000, $this->middleware->normalizeRupiahValue('100.000'));
        $this->assertSame(1000000, $this->middleware->normalizeRupiahValue('1.000.000'));
        $this->assertSame(15000000, $this->middleware->normalizeRupiahValue('15.000.000'));
        $this->assertSame(250000, $this->middleware->normalizeRupiahValue('250.000'));
    }

    public function test_normalizes_rupiah_with_rp_prefix_and_trailing_cents(): void
    {
        $this->assertSame(15000000, $this->middleware->normalizeRupiahValue('Rp 15.000.000'));
        $this->assertSame(15000000, $this->middleware->normalizeRupiahValue('rp 15.000.000'));
        $this->assertSame(15000000, $this->middleware->normalizeRupiahValue('IDR 15.000.000'));
        $this->assertSame(1500000, $this->middleware->normalizeRupiahValue('1.500.000,00'));
        $this->assertSame(1500000, $this->middleware->normalizeRupiahValue('Rp 1.500.000,00'));
    }

    public function test_normalizes_decimal_comma_formats(): void
    {
        // Thousand dot with decimal comma
        $this->assertSame(1500.5, $this->middleware->normalizeRupiahValue('1.500,50'));
        $this->assertSame(1500.75, $this->middleware->normalizeRupiahValue('1.500,75'));
        $this->assertSame(1500, $this->middleware->normalizeRupiahValue('1.500,00'));

        // Plain digits with decimal comma (no thousand dots)
        $this->assertSame(1500.5, $this->middleware->normalizeRupiahValue('1500,50'));
        $this->assertSame(1500, $this->middleware->normalizeRupiahValue('1500,00'));
        $this->assertSame(25.5, $this->middleware->normalizeRupiahValue('25,5'));
    }

    public function test_normalizes_standard_dot_decimal_floats(): void
    {
        $this->assertSame(1500.5, $this->middleware->normalizeRupiahValue('1500.50'));
        $this->assertSame(12.5, $this->middleware->normalizeRupiahValue('12.5'));
        $this->assertSame(0.75, $this->middleware->normalizeRupiahValue('0.75'));
    }

    public function test_preserves_negative_values(): void
    {
        $this->assertSame(-1000000, $this->middleware->normalizeRupiahValue('-1.000.000'));
        $this->assertSame(-1000000, $this->middleware->normalizeRupiahValue('Rp -1.000.000'));
        $this->assertSame(-1000000, $this->middleware->normalizeRupiahValue('-Rp 1.000.000'));
        $this->assertSame(-1500.5, $this->middleware->normalizeRupiahValue('-1.500,50'));
        $this->assertSame(-500, $this->middleware->normalizeRupiahValue('-500'));
    }

    public function test_preserves_unformatted_integers_and_empty_strings(): void
    {
        $this->assertSame(1000000, $this->middleware->normalizeRupiahValue('1000000'));
        $this->assertSame(0, $this->middleware->normalizeRupiahValue('0'));
        $this->assertSame('', $this->middleware->normalizeRupiahValue(''));
        $this->assertSame('   ', $this->middleware->normalizeRupiahValue('   '));
    }

    public function test_leaves_invalid_or_ambiguous_formats_untouched_to_not_bypass_validation(): void
    {
        // Malformed dot groupings should NOT be converted to valid numbers
        $this->assertSame('12.34.56', $this->middleware->normalizeRupiahValue('12.34.56'));
        $this->assertSame('1.00.000', $this->middleware->normalizeRupiahValue('1.00.000'));
        $this->assertSame('abc.123', $this->middleware->normalizeRupiahValue('abc.123'));
        $this->assertSame('invalid-price', $this->middleware->normalizeRupiahValue('invalid-price'));
        $this->assertSame('Rp invalid', $this->middleware->normalizeRupiahValue('Rp invalid'));
    }

    public function test_normalizes_financial_keys_in_nested_array(): void
    {
        $input = [
            'shipping_fee' => '250.000',
            'cost' => '1.750.000',
            'deposit_amount' => '5.000.000',
            'items' => [
                [
                    'name' => 'Forklift 3T',
                    'quantity' => '2.5',            // Non-financial decimal: MUST NOT be changed
                    'duration' => '3',               // Non-financial integer: MUST NOT be changed
                    'unit_price' => '35.000.000',    // Financial: MUST be normalized
                    'discount' => '500.000',          // Financial: MUST be normalized
                ],
            ],
            'products' => [
                [
                    'unit_cost' => '12.000.000',     // Financial: MUST be normalized
                    'shipping_fee' => '150.000',     // Financial: MUST be normalized
                    'quantity' => '1.5',             // Non-financial: MUST NOT be changed
                ],
            ],
            'phone' => '0812.3456.7890',             // Non-financial phone: MUST NOT be changed
            'npwp' => '01.234.567.8-901.000',        // Non-financial NPWP: MUST NOT be changed
            'serial_number' => 'SN-FL-9988',         // Non-financial serial: MUST NOT be changed
            'order_number' => 'SO-2024.10.01',       // Non-financial order code: MUST NOT be changed
            'tax_percent' => '11',                   // Non-financial percent: MUST NOT be changed
            'hour_meter' => '120.5',                 // Non-financial decimal: MUST NOT be changed
            'year' => '2024',                        // Non-financial: MUST NOT be changed
        ];

        $normalized = $this->middleware->normalizeArray($input);

        // Financial fields should be clean numbers
        $this->assertSame(250000, $normalized['shipping_fee']);
        $this->assertSame(1750000, $normalized['cost']);
        $this->assertSame(5000000, $normalized['deposit_amount']);
        $this->assertSame(35000000, $normalized['items'][0]['unit_price']);
        $this->assertSame(500000, $normalized['items'][0]['discount']);
        $this->assertSame(12000000, $normalized['products'][0]['unit_cost']);
        $this->assertSame(150000, $normalized['products'][0]['shipping_fee']);

        // Non-financial fields must remain completely unchanged
        $this->assertSame('2.5', $normalized['items'][0]['quantity']);
        $this->assertSame('3', $normalized['items'][0]['duration']);
        $this->assertSame('1.5', $normalized['products'][0]['quantity']);
        $this->assertSame('0812.3456.7890', $normalized['phone']);
        $this->assertSame('01.234.567.8-901.000', $normalized['npwp']);
        $this->assertSame('SN-FL-9988', $normalized['serial_number']);
        $this->assertSame('SO-2024.10.01', $normalized['order_number']);
        $this->assertSame('11', $normalized['tax_percent']);
        $this->assertSame('120.5', $normalized['hour_meter']);
        $this->assertSame('2024', $normalized['year']);
    }

    public function test_middleware_handles_http_request(): void
    {
        $request = Request::create('/erp/procurement/orders', 'POST', [
            'shipping_fee' => '500.000',
            'rental_duration' => '2',
            'customer_po_number' => 'PO.2024.999',
            'items' => [
                [
                    'name' => 'Oli Hidrolik',
                    'quantity' => '10',
                    'unit_price' => '1.200.000',
                ]
            ]
        ]);

        $response = $this->middleware->handle($request, function ($req) {
            $this->assertSame(500000, $req->input('shipping_fee'));
            $this->assertSame('2', $req->input('rental_duration'));
            $this->assertSame('PO.2024.999', $req->input('customer_po_number'));
            $this->assertSame(1200000, $req->input('items.0.unit_price'));
            $this->assertSame('10', $req->input('items.0.quantity'));
            return new Response('OK');
        });

        $this->assertSame('OK', $response->getContent());
    }
}
