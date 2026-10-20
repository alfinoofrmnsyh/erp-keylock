<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerSalesIncentiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $salesUserA;
    protected User $salesUserB;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator', 'is_system' => true]);
        $salesRole = Role::firstOrCreate(['name' => 'sales'], ['label' => 'Sales Representative', 'is_system' => false]);

        $this->adminUser = User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'admin@keylock.id',
            'role' => 'admin',
            'usergrpid' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->salesUserA = User::factory()->create([
            'name' => 'Sales Person A',
            'email' => 'sales.a@keylock.id',
            'role' => 'sales',
            'usergrpid' => $salesRole->id,
            'is_active' => true,
        ]);

        $this->salesUserB = User::factory()->create([
            'name' => 'Sales Person B',
            'email' => 'sales.b@keylock.id',
            'role' => 'sales',
            'usergrpid' => $salesRole->id,
            'is_active' => true,
        ]);
    }

    /**
     * 1. Test master customer can be created and updated with Sales PIC and CRM Leads origin.
     */
    public function test_master_customer_stores_sales_by_and_lead_origin(): void
    {
        $lead = Lead::create([
            'code' => 'LEAD-202610-001',
            'company_name' => 'PT Sumber Makmur Jaya',
            'pic_name' => 'Budi Santoso',
            'pic_phone' => '08123456789',
            'sales_id' => $this->salesUserA->id,
            'pipeline_stage' => 'qualified',
            'priority' => 'high',
        ]);

        // Create customer via API/POST
        $response = $this->actingAs($this->adminUser)->postJson(route('erp.master_data.contact.store'), [
            'type' => 'customer',
            'name' => 'PT Sumber Makmur Jaya',
            'pic_name' => 'Budi Santoso',
            'phone' => '08123456789',
            'lead_id' => $lead->id,
            'sales_id' => $this->salesUserA->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', [
            'name' => 'PT Sumber Makmur Jaya',
            'lead_id' => $lead->id,
            'lead_code' => 'LEAD-202610-001',
            'sales_id' => $this->salesUserA->id,
            'sales_name' => 'Sales Person A',
        ]);

        $customer = Contact::where('name', 'PT Sumber Makmur Jaya')->first();
        $this->assertEquals($lead->id, $customer->lead_id);
        $this->assertEquals('LEAD-202610-001', $customer->lead_code);
        $this->assertEquals($this->salesUserA->id, $customer->sales_id);

        // Update customer PIC to Sales B
        $updateResponse = $this->actingAs($this->adminUser)->postJson("/erp/master-data/contact/{$customer->id}/update", [
            'name' => 'PT Sumber Makmur Jaya TBK',
            'sales_id' => $this->salesUserB->id,
            'lead_id' => $lead->id,
        ]);

        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('contacts', [
            'id' => $customer->id,
            'name' => 'PT Sumber Makmur Jaya TBK',
            'sales_id' => $this->salesUserB->id,
            'sales_name' => 'Sales Person B',
        ]);
    }

    /**
     * 2. Test CRM lead conversion automatically populates sales_id, sales_name, lead_id, lead_code.
     */
    public function test_crm_lead_conversion_populates_sales_and_lead_fields(): void
    {
        $lead = Lead::create([
            'code' => 'LEAD-202610-099',
            'company_name' => 'PT Industri Logam Mulia',
            'pic_name' => 'Hendro',
            'pic_phone' => '08987654321',
            'pic_email' => 'hendro@logam.com',
            'address' => 'Kawasan Industri Cikarang',
            'sales_id' => $this->salesUserA->id,
            'pipeline_stage' => 'qualified',
            'priority' => 'high',
        ]);

        // Test prepareQuotation endpoint
        $res = $this->actingAs($this->salesUserA)->postJson(route('erp.crm.leads.prepare_quotation', $lead->id));
        $res->assertStatus(200);

        $customer = Contact::where('name', 'PT Industri Logam Mulia')->first();
        $this->assertNotNull($customer);
        $this->assertEquals($this->salesUserA->id, $customer->sales_id);
        $this->assertEquals('Sales Person A', $customer->sales_name);
        $this->assertEquals($lead->id, $customer->lead_id);
        $this->assertEquals('LEAD-202610-099', $customer->lead_code);
    }

    /**
     * 3. Test continuous sales attribution:
     * When Admin creates a quotation for a Customer acquired by Sales A,
     * the quotation is permanently attributed to Sales A (created_by = Sales A)
     * as long as Sales A is active.
     */
    public function test_admin_creating_quotation_attributes_incentive_to_active_sales_pic(): void
    {
        $customer = Contact::create([
            'code' => 'CUST-001',
            'type' => 'customer',
            'name' => 'Customer 001 PT Sejahtera',
            'sales_id' => $this->salesUserA->id,
            'sales_name' => $this->salesUserA->name,
            'lead_code' => 'LEAD-001',
            'is_active' => true,
        ]);

        $item = Item::create([
            'sku' => 'FL-30',
            'name' => 'Forklift 3 Ton Diesel',
            'category' => 'Forklift',
            'rental_rate_monthly' => 12000000,
            'is_active' => true,
        ]);

        // Admin creates quotation for Customer 001
        $res = $this->actingAs($this->adminUser)->postJson(route('erp.quotations.store'), [
            'contact_id' => $customer->id,
            'order_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'item_id' => $item->id,
                    'transaction_type' => 'rental',
                    'name' => $item->name,
                    'quantity' => 1,
                    'unit_price' => 12000000,
                    'duration' => 1,
                    'duration_unit' => 'Bulan',
                ]
            ],
        ]);

        $res->assertStatus(200);

        $quotation = Order::where('contact_id', $customer->id)->where('order_type', 'quotation')->first();
        $this->assertNotNull($quotation);

        // Crucial requirement: created_by should be Sales User A, NOT Admin!
        $this->assertEquals($this->salesUserA->id, $quotation->created_by);
        $this->assertEquals($this->salesUserA->name, $quotation->creator->name);

        // When quotation is converted to rental order by Admin, order attribution stays with Sales A
        $quotation->status = 'approved';
        $quotation->save();

        $convertRes = $this->actingAs($this->adminUser)->postJson(route('erp.orders.convert', $quotation->id));
        $convertRes->assertStatus(200);

        $rentalOrder = Order::where('parent_id', $quotation->id)->first();
        $this->assertNotNull($rentalOrder);
        $this->assertEquals($this->salesUserA->id, $rentalOrder->created_by);
    }

    /**
     * 4. Test attribution fallback when Sales PIC is inactive.
     */
    public function test_inactive_sales_falls_back_to_creator(): void
    {
        // Deactivate Sales A
        $this->salesUserA->is_active = false;
        $this->salesUserA->save();

        $customer = Contact::create([
            'code' => 'CUST-002',
            'type' => 'customer',
            'name' => 'Customer 002 PT Mandiri',
            'sales_id' => $this->salesUserA->id,
            'sales_name' => $this->salesUserA->name,
            'is_active' => true,
        ]);

        // Admin creates quotation for Customer 002
        $res = $this->actingAs($this->adminUser)->postJson(route('erp.quotations.store'), [
            'contact_id' => $customer->id,
            'order_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'name' => 'Forklift 5 Ton',
                    'quantity' => 1,
                    'unit_price' => 20000000,
                    'duration' => 1,
                ]
            ],
        ]);

        $res->assertStatus(200);

        $quotation = Order::where('contact_id', $customer->id)->where('order_type', 'quotation')->first();
        $this->assertNotNull($quotation);

        // Because Sales User A is inactive, falls back to Admin!
        $this->assertEquals($this->adminUser->id, $quotation->created_by);
    }
}
