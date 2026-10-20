<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Contact;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\Asset;
use App\Models\Item;
use App\Models\AssetService;
use App\Models\Payment;

class RupiahFormattingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $financeUser;
    protected User $operationalUser;
    protected Contact $customerContact;
    protected Contact $vendorContact;
    protected Item $catalogItem;
    protected Asset $testAsset;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator', 'is_system' => true]);
        $financeRole = Role::firstOrCreate(['name' => 'finance'], ['label' => 'Finance & Accounting', 'is_system' => false]);
        $opsRole = Role::firstOrCreate(['name' => 'operasional'], ['label' => 'Operasional & Fleet', 'is_system' => false]);

        // 2. Users
        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@keylock.id',
            'role' => 'admin',
            'usergrpid' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->financeUser = User::factory()->create([
            'name' => 'Finance Staff',
            'email' => 'finance@keylock.id',
            'role' => 'finance',
            'usergrpid' => $financeRole->id,
            'is_active' => true,
        ]);

        $this->operationalUser = User::factory()->create([
            'name' => 'Ops Staff',
            'email' => 'ops@keylock.id',
            'role' => 'operasional',
            'usergrpid' => $opsRole->id,
            'is_active' => true,
        ]);

        // 3. Contacts
        $this->customerContact = Contact::create([
            'type' => 'customer',
            'category' => 'Customer',
            'code' => 'CUST-001',
            'name' => 'PT Mitra Konstruksi Utama',
            'is_active' => true,
        ]);

        $this->vendorContact = Contact::create([
            'type' => 'vendor',
            'category' => 'Vendor Unit & Alat Berat',
            'code' => 'VND-001',
            'name' => 'PT Sumber Alat Berat',
            'is_active' => true,
        ]);

        // 4. Catalog Item & Asset
        $this->catalogItem = Item::create([
            'sku' => 'PRD-FL-001',
            'name' => 'Forklift 3 Ton Diesel',
            'category' => 'Forklift',
            'unit' => 'Unit',
            'item_type' => 'rental_unit',
            'rental_rate_monthly' => 15000000,
            'sale_price' => 250000000,
            'is_active' => true,
        ]);

        $this->testAsset = Asset::create([
            'item_id' => $this->catalogItem->id,
            'asset_code' => 'AST-FL-001',
            'serial_number' => 'SN-FL-9988',
            'ownership' => 'company',
            'status' => 'available',
            'year' => 2023,
            'hour_meter' => 120.5,
            'current_location' => 'Pool Utama Cikarang',
        ]);
    }

    /**
     * 1. Test Quotation creation accepts formatted Rupiah strings with dot thousands separators
     *    and stores exact integer numbers in database.
     */
    public function test_quotation_creation_with_formatted_rupiah_inputs_succeeds_and_stores_exact_values(): void
    {
        $payload = [
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(14)->format('Y-m-d'),
            'customer_po_number' => 'PO-CUST-8899',
            'has_shipping' => '1',
            'shipping_delivery_fee' => '250.000', // Formatted input
            'shipping_return_fee' => '250.000',   // Formatted input
            'include_ppn' => '1',
            'items' => [
                [
                    'item_id' => $this->catalogItem->id,
                    'name' => $this->catalogItem->name,
                    'transaction_type' => 'rental',
                    'quantity' => '1',           // Non-financial integer count
                    'duration' => '1',           // Non-financial duration
                    'duration_unit' => 'Bulan',
                    'unit_price' => '1.000.000', // Formatted Rupiah nominal
                    'discount' => '0',
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)->post('/erp/quotations', $payload);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        // Verify order saved to DB
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(250000, (float)$order->shipping_delivery_fee);
        $this->assertEquals(250000, (float)$order->shipping_return_fee);
        $this->assertEquals(500000, (float)$order->shipping_fee);

        // Verify order item saved with exact clean numeric
        $item = $order->items->first();
        $this->assertNotNull($item);
        $this->assertEquals(1000000, (float)$item->unit_price);
        $this->assertEquals(1, (float)$item->quantity);

        // Verify calculations: subtotal = 1.000.000, ppn 11% = 110.000, total = 1.610.000
        $this->assertEquals(1000000, (float)$order->subtotal);
        $this->assertEquals(110000, (float)$order->tax_amount);
        $this->assertEquals(1610000, (float)$order->total_amount);
    }

    /**
     * 2. Test Procurement PO with formatted Rupiah strings is normalized and stored accurately.
     */
    public function test_procurement_order_with_formatted_rupiah_inputs_stores_clean_numerics(): void
    {
        $payload = [
            'contact_id' => $this->vendorContact->id,
            'procurement_type' => 'purchase_unit',
            'order_date' => now()->format('Y-m-d'),
            'expected_delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'payment_terms' => 'Net 30 Days',
            'shipping_fee' => '500.000', // Formatted Rupiah
            'include_ppn' => '1',
            'items' => [
                [
                    'name' => 'Sparepart Mast Forklift',
                    'quantity' => '2',             // Non-financial count
                    'unit' => 'Pcs',
                    'unit_price' => '15.000.000',   // Formatted Rupiah nominal
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)->post('/erp/procurement/orders', $payload);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $po = Order::where('order_type', 'purchase_order')->latest()->first();
        $this->assertNotNull($po);
        $this->assertEquals(500000, (float)$po->shipping_fee);

        $poItem = $po->items->first();
        $this->assertNotNull($poItem);
        $this->assertEquals(15000000, (float)$poItem->unit_price);
        $this->assertEquals(2, (float)$poItem->quantity);

        // Subtotal = 30.000.000, PPN = 3.300.000, Shipping = 500.000 => Total = 33.800.000
        $this->assertEquals(30000000, (float)$po->subtotal);
        $this->assertEquals(3300000, (float)$po->tax_amount);
        $this->assertEquals(33800000, (float)$po->total_amount);
    }

    /**
     * 3. Test Fleet Service record with formatted cost and non-financial decimal hour meter.
     */
    public function test_fleet_service_with_formatted_cost_and_decimal_hour_meter(): void
    {
        $payload = [
            'service_date' => now()->format('Y-m-d'),
            'service_type' => 'periodic',
            'hour_meter' => '150.5',       // Non-financial decimal: must NOT be stripped of dot
            'cost' => '750.000',           // Financial: must be stripped to 750000
            'status' => 'completed',
            'work_description' => 'Ganti oli mesin dan filter hidrolik berkala.',
        ];

        $response = $this->actingAs($this->operationalUser)
            ->post("/erp/fleet/{$this->testAsset->id}/service", $payload);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $service = AssetService::where('asset_id', $this->testAsset->id)->latest()->first();
        $this->assertNotNull($service);
        $this->assertEquals(750000, (float)$service->cost);
        $this->assertEquals(150.5, (float)$service->hour_meter);
    }

    /**
     * 4. Test Finance Invoice Payment with formatted Rupiah updates outstanding correctly.
     */
    public function test_invoice_payment_recording_with_formatted_amount_updates_outstanding(): void
    {
        // Create an invoice with total 5.000.000
        $invoice = Invoice::create([
            'invoice_number' => 'INV/TEST/001',
            'invoice_type' => 'customer_invoice',
            'contact_id' => $this->customerContact->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'subtotal' => 5000000,
            'tax_amount' => 0,
            'total_amount' => 5000000,
            'paid_amount' => 0,
            'balance_due' => 5000000,
            'status' => 'sent',
        ]);

        $payload = [
            'invoice_id' => $invoice->id,
            'payment_date' => now()->format('Y-m-d'),
            'amount' => '2.000.000', // Formatted Rupiah partial payment
            'payment_method' => 'bank_transfer',
            'bank_account' => 'BCA - PT KEY LOCK INDONESIA',
        ];

        $response = $this->actingAs($this->financeUser)
            ->post('/erp/finance/payments', $payload);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        // Check payment record
        $payment = Payment::where('invoice_id', $invoice->id)->latest()->first();
        $this->assertNotNull($payment);
        $this->assertEquals(2000000, (float)$payment->amount);

        // Check updated invoice balances
        $invoice->refresh();
        $this->assertEquals(2000000, (float)$invoice->paid_amount);
        $this->assertEquals(3000000, (float)$invoice->balance_due);
        $this->assertSame('partial', $invoice->status);
    }

    /**
     * 5. Test Zero and empty nominals are handled cleanly without error.
     */
    public function test_zero_and_empty_nominal_handled_cleanly(): void
    {
        $payload = [
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'has_shipping' => '0',
            'include_ppn' => '0',
            'shipping_delivery_fee' => '0', // Zero formatted
            'shipping_return_fee' => '',    // Empty string
            'items' => [
                [
                    'name' => 'Jasa Sewa',
                    'transaction_type' => 'rental',
                    'quantity' => '1',
                    'duration' => '1',
                    'unit_price' => '500.000',
                    'discount' => '0',
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)->post('/erp/quotations', $payload);
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $order = Order::latest()->first();
        $this->assertEquals(0, (float)$order->shipping_delivery_fee);
        $this->assertEquals(500000, (float)$order->total_amount);
    }

    /**
     * 6. Test Validation failure retains input without string mangling or duplication.
     */
    public function test_validation_failure_retains_clean_numeric_input(): void
    {
        // Omit required contact_id to intentionally trigger validation error
        $payload = [
            'order_date' => now()->format('Y-m-d'),
            'shipping_delivery_fee' => '1.000.000',
            'items' => [
                [
                    'name' => 'Forklift',
                    'quantity' => '1',
                    'unit_price' => '5.000.000',
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)->post('/erp/quotations', $payload);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['contact_id']);

        // Check that old input in session has normalized numeric values
        $this->assertEquals(1000000, session('_old_input.shipping_delivery_fee'));
        $this->assertEquals(5000000, session('_old_input.items.0.unit_price'));
    }

    /**
     * 7. Test Views render masked attributes for frontend forms.
     */
    public function test_views_contain_data_rupiah_attributes(): void
    {
        $resFleet = $this->actingAs($this->operationalUser)->get('/erp/fleet');
        $resFleet->assertStatus(200);
        $resFleet->assertSee('data-rupiah');

        $resProcurement = $this->actingAs($this->adminUser)->get('/erp/procurement');
        $resProcurement->assertStatus(200);
        $resProcurement->assertSee('data-rupiah');

        $resFinance = $this->actingAs($this->financeUser)->get('/erp/finance');
        $resFinance->assertStatus(200);
        $resFinance->assertSee('data-rupiah');
    }

    /**
     * 8. Negative test: Negative formatted input fails Laravel min:0 validation.
     */
    public function test_negative_formatted_nominal_fails_validation(): void
    {
        $payload = [
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'shipping_delivery_fee' => '0',
            'items' => [
                [
                    'name' => 'Forklift',
                    'transaction_type' => 'rental',
                    'quantity' => '1',
                    'duration' => '1',
                    'unit_price' => '-1.000.000', // Formatted negative price
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)->post('/erp/quotations', $payload);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['items.0.unit_price']);
    }

    /**
     * 9. Negative test: Malformed non-numeric text fails Laravel numeric validation.
     */
    public function test_malformed_non_numeric_input_fails_numeric_validation(): void
    {
        $payload = [
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'shipping_delivery_fee' => '0',
            'items' => [
                [
                    'name' => 'Forklift',
                    'transaction_type' => 'rental',
                    'quantity' => '1',
                    'duration' => '1',
                    'unit_price' => 'abc.def', // Invalid text
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)->post('/erp/quotations', $payload);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['items.0.unit_price']);
    }

    /**
     * 10. Test decimal comma input is accurately normalized and stored.
     */
    public function test_decimal_comma_input_stored_accurately(): void
    {
        $payload = [
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'shipping_delivery_fee' => '150.500,50', // Dot thousands with decimal comma
            'has_shipping' => '1',
            'items' => [
                [
                    'name' => 'Forklift',
                    'transaction_type' => 'rental',
                    'quantity' => '1',
                    'duration' => '1',
                    'unit_price' => '1.500.000,50', // Dot thousands with decimal comma
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)->post('/erp/quotations', $payload);
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $order = Order::latest()->first();
        $this->assertEquals(150500.50, (float)$order->shipping_delivery_fee);
        $this->assertEquals(1500000.50, (float)$order->items->first()->unit_price);
    }
}
