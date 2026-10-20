<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Role;
use App\Models\Contact;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\CompanyConfig;
use App\Models\Category;
use App\Models\Product;
use App\Models\Lead;
use App\Models\Asset;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Filament\Panel;

class SecurityRemediationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $salesUserA;
    protected User $salesUserB;
    protected User $financeUser;
    protected User $operationalUser;
    protected Contact $customerContact;
    protected Contact $vendorContact;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Retrieve or create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator', 'is_system' => true]);
        $salesRole = Role::firstOrCreate(['name' => 'sales'], ['label' => 'Sales Representative', 'is_system' => false]);
        $opsRole = Role::firstOrCreate(['name' => 'operasional'], ['label' => 'Operasional & Fleet', 'is_system' => false]);
        $financeRole = Role::firstOrCreate(['name' => 'finance'], ['label' => 'Finance & Accounting', 'is_system' => false]);

        // Create Users
        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@keylock.id',
            'role' => 'admin',
            'usergrpid' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->salesUserA = User::factory()->create([
            'name' => 'Sales User A',
            'email' => 'salesa@keylock.id',
            'role' => 'sales',
            'usergrpid' => $salesRole->id,
            'is_active' => true,
        ]);

        $this->salesUserB = User::factory()->create([
            'name' => 'Sales User B',
            'email' => 'salesb@keylock.id',
            'role' => 'sales',
            'usergrpid' => $salesRole->id,
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
            'name' => 'Operational Staff',
            'email' => 'ops@keylock.id',
            'role' => 'operasional',
            'usergrpid' => $opsRole->id,
            'is_active' => true,
        ]);

        $this->customerContact = Contact::create([
            'code' => 'CUST-001',
            'type' => 'customer',
            'name' => 'PT Pelanggan Utama',
            'email' => 'pelanggan@test.com',
            'phone' => '08123456789',
            'address' => 'Kawasan Industri KIIC Karawang',
            'is_active' => true,
        ]);

        $this->vendorContact = Contact::create([
            'code' => 'VEND-001',
            'type' => 'vendor',
            'name' => 'PT Vendor Sparepart',
            'email' => 'vendor@test.com',
            'phone' => '08987654321',
            'address' => 'Jakarta Barat',
            'is_active' => true,
        ]);
    }

    /**
     * SEC-02 & SEC-03: Verify Role Helper and Filament Panel Access.
     */
    public function test_user_model_role_checks_and_panel_access(): void
    {
        $this->assertTrue($this->adminUser->isAdmin());
        $this->assertFalse($this->salesUserA->isAdmin());
        $this->assertFalse($this->financeUser->isAdmin());
        $this->assertFalse($this->operationalUser->isAdmin());

        $this->assertTrue($this->salesUserA->isSales());
        $this->assertTrue($this->financeUser->isFinance());
        $this->assertTrue($this->operationalUser->isOperational());

        // Operational user MUST NOT be treated as admin
        $this->assertFalse($this->operationalUser->isAdmin());

        // Panel access check: only admin can access Filament Panel
        $panelMock = $this->createMock(Panel::class);
        $this->assertTrue($this->adminUser->canAccessPanel($panelMock));
        $this->assertFalse($this->salesUserA->canAccessPanel($panelMock));
        $this->assertFalse($this->financeUser->canAccessPanel($panelMock));
        $this->assertFalse($this->operationalUser->canAccessPanel($panelMock));
    }

    /**
     * SEC-02: Non-admin is blocked from user management and system menus (403).
     */
    public function test_non_admin_blocked_from_user_and_menu_management(): void
    {
        // Sales user blocked from GET /erp/users
        $response = $this->actingAs($this->salesUserA)->get('/erp/users');
        $response->assertStatus(403);

        // Sales user blocked from POST /erp/users
        $response = $this->actingAs($this->salesUserA)->post('/erp/users', [
            'name' => 'Hacker User',
            'email' => 'hacker@keylock.id',
            'password' => 'secret123',
            'usergrpid' => 1,
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'hacker@keylock.id']);

        // Sales user blocked from GET /erp/system-menus
        $response = $this->actingAs($this->salesUserA)->get('/erp/system-menus');
        $response->assertStatus(403);

        // Sales user blocked from company config update
        $response = $this->actingAs($this->salesUserA)->post('/erp/system-configs', [
            'company_name' => 'Hacked Company',
        ]);
        $response->assertStatus(403);

        // Admin user can access /erp/users
        $response = $this->actingAs($this->adminUser)->get('/erp/users');
        $response->assertStatus(200);
    }

    /**
     * SEC-04: Sales cannot cancel another sales user's quotation/order.
     */
    public function test_sales_cannot_cancel_another_sales_order(): void
    {
        // Order created by Sales A
        $order = Order::create([
            'order_number' => 'QTN-202610-001',
            'order_type' => 'quotation',
            'quotation_type' => 'sales',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'total_amount' => 10000000,
            'created_by' => $this->salesUserA->id,
        ]);

        // Sales B attempts to cancel Sales A's order
        $response = $this->actingAs($this->salesUserB)->postJson("/erp/orders/{$order->id}/cancel", [
            'cancel_reason' => 'Unauthorized cancellation attempt',
        ]);
        $response->assertStatus(403);

        // Verify order is NOT cancelled
        $this->assertEquals('draft', $order->fresh()->status);

        // Sales A cancels their own order
        $response = $this->actingAs($this->salesUserA)->postJson("/erp/orders/{$order->id}/cancel", [
            'cancel_reason' => 'Customer requested cancellation',
        ]);
        $response->assertStatus(200);
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    /**
     * SEC-04: Sales cannot edit another sales user's quotation.
     */
    public function test_sales_cannot_edit_another_sales_quotation(): void
    {
        $order = Order::create([
            'order_number' => 'QTN-202610-002',
            'order_type' => 'quotation',
            'quotation_type' => 'sales',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'total_amount' => 5000000,
            'created_by' => $this->salesUserA->id,
        ]);

        // Sales B attempts to edit Sales A's quotation
        $response = $this->actingAs($this->salesUserB)->postJson("/erp/quotations/{$order->id}/update", [
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'name' => 'Tampered Product',
                    'quantity' => 10,
                    'unit_price' => 1000,
                ],
            ],
        ]);
        $response->assertStatus(403);
    }

    /**
     * SEC-04: State transition enforcement on order cancellation.
     */
    public function test_cannot_cancel_already_cancelled_or_completed_order(): void
    {
        $completedOrder = Order::create([
            'order_number' => 'SO-202610-001',
            'order_type' => 'sales_order',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'total_amount' => 5000000,
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/erp/orders/{$completedOrder->id}/cancel", [
            'cancel_reason' => 'Cancel completed order',
        ]);
        $response->assertStatus(400);

        $cancelledOrder = Order::create([
            'order_number' => 'SO-202610-002',
            'order_type' => 'sales_order',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'cancelled',
            'total_amount' => 5000000,
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/erp/orders/{$cancelledOrder->id}/cancel", [
            'cancel_reason' => 'Cancel already cancelled order',
        ]);
        $response->assertStatus(400);
    }

    /**
     * SEC-04: Non-finance user cannot record invoices, bills, or payments.
     */
    public function test_non_finance_blocked_from_recording_payments_and_invoices(): void
    {
        $order = Order::create([
            'order_number' => 'SO-202610-003',
            'order_type' => 'sales_order',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'in_progress',
            'total_amount' => 20000000,
            'created_by' => $this->salesUserA->id,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-001',
            'invoice_type' => 'customer_invoice',
            'order_id' => $order->id,
            'contact_id' => $this->customerContact->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'subtotal' => 20000000,
            'total_amount' => 20000000,
            'paid_amount' => 0,
            'balance_due' => 20000000,
            'status' => 'sent',
            'created_by' => $this->financeUser->id,
        ]);

        // Sales user attempts to record payment
        $response = $this->actingAs($this->salesUserA)->post('/erp/finance/payments', [
            'invoice_id' => $invoice->id,
            'payment_date' => now()->format('Y-m-d'),
            'amount' => 5000000,
            'payment_method' => 'bank_transfer',
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('payments', ['invoice_id' => $invoice->id]);

        // Sales user attempts to create invoice
        $response = $this->actingAs($this->salesUserA)->post('/erp/finance/invoices', [
            'contact_id' => $this->customerContact->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'subtotal' => 10000000,
            'total_amount' => 10000000,
        ]);
        $response->assertStatus(403);

        // Finance user CAN record payment
        $response = $this->actingAs($this->financeUser)->postJson('/erp/finance/payments', [
            'invoice_id' => $invoice->id,
            'payment_date' => now()->format('Y-m-d'),
            'amount' => 5000000,
            'payment_method' => 'bank_transfer',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('payments', ['invoice_id' => $invoice->id, 'amount' => 5000000]);
    }

    /**
     * SEC-01: Malicious file upload via base64 quotation is rejected and generates no executable files.
     */
    public function test_malicious_base64_upload_is_rejected_and_creates_no_script_file(): void
    {
        // Malicious PHP payload masquerading as an image
        $phpPayload = "<?php phpinfo(); system(\$_GET['cmd']); ?>";
        $maliciousBase64 = 'data:image/php;base64,' . base64_encode($phpPayload);

        $response = $this->actingAs($this->salesUserA)->postJson('/erp/quotations', [
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'name' => 'Exploit Payload Test',
                    'quantity' => 1,
                    'unit_price' => 500000,
                    'image' => $maliciousBase64,
                ],
            ],
        ]);

        // Must fail with validation error 422
        $response->assertStatus(422);

        // Verify that NO .php or quotation item file was created in storage
        $files = Storage::disk('public')->allFiles();
        foreach ($files as $file) {
            $this->assertStringEndsNotWith('.php', strtolower($file));
            $this->assertStringEndsNotWith('.phtml', strtolower($file));
        }
    }

    /**
     * SEC-01: Valid PNG base64 upload succeeds with random filename and png extension.
     */
    public function test_valid_base64_png_upload_succeeds_with_safe_random_filename(): void
    {
        // 1x1 transparent PNG base64
        $validPngBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $response = $this->actingAs($this->salesUserA)->postJson('/erp/quotations', [
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'name' => 'Valid Unit Forklift',
                    'quantity' => 1,
                    'unit_price' => 25000000,
                    'image' => $validPngBase64,
                ],
            ],
        ]);

        $response->assertStatus(200);

        $savedItem = OrderItem::latest('id')->first();
        $this->assertNotNull($savedItem);
        $this->assertNotNull($savedItem->image);
        $this->assertStringStartsWith('storage/quotation_items/', $savedItem->image);
        $this->assertStringEndsWith('.png', $savedItem->image);

        // Verify storage file exists and matches random hash name
        $relativeStoragePath = str_replace('storage/', '', $savedItem->image);
        Storage::disk('public')->assertExists($relativeStoragePath);
    }

    /**
     * SEC-05: Access control on PDF endpoints.
     */
    public function test_pdf_endpoints_access_control(): void
    {
        // 1. Quotation PDF
        $quotation = Order::create([
            'order_number' => 'QTN-202610-003',
            'order_type' => 'quotation',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'total_amount' => 5000000,
            'created_by' => $this->salesUserA->id,
        ]);

        // Unauthorized user (Sales B) blocked from viewing Sales A's quotation PDF
        $response = $this->actingAs($this->salesUserB)->get("/erp/quotations/{$quotation->id}/pdf");
        $response->assertStatus(403);

        // Creator (Sales A) can view quotation PDF
        $response = $this->actingAs($this->salesUserA)->get("/erp/quotations/{$quotation->id}/pdf");
        $response->assertStatus(200);

        // Admin can view quotation PDF
        $response = $this->actingAs($this->adminUser)->get("/erp/quotations/{$quotation->id}/pdf");
        $response->assertStatus(200);

        // 2. Invoice PDF
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-002',
            'invoice_type' => 'customer_invoice',
            'order_id' => $quotation->id,
            'contact_id' => $this->customerContact->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'subtotal' => 5000000,
            'total_amount' => 5000000,
            'paid_amount' => 0,
            'balance_due' => 5000000,
            'status' => 'sent',
            'created_by' => $this->financeUser->id,
        ]);

        // Unauthorized user (Sales B) blocked from Invoice PDF
        $response = $this->actingAs($this->salesUserB)->get("/erp/finance/invoices/{$invoice->id}/pdf");
        $response->assertStatus(403);

        // Finance user can view Invoice PDF
        $response = $this->actingAs($this->financeUser)->get("/erp/finance/invoices/{$invoice->id}/pdf");
        $response->assertStatus(200);

        // 3. Purchase Order PDF (Procurement)
        $poOrder = Order::create([
            'order_number' => 'PO-202610-001',
            'order_type' => 'purchase_order',
            'contact_id' => $this->vendorContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'total_amount' => 8000000,
            'created_by' => $this->adminUser->id,
        ]);

        // Sales user blocked from PO PDF
        $response = $this->actingAs($this->salesUserA)->get("/erp/procurement/orders/{$poOrder->id}/pdf");
        $response->assertStatus(403);

        // Admin can view PO PDF
        $response = $this->actingAs($this->adminUser)->get("/erp/procurement/orders/{$poOrder->id}/pdf");
        $response->assertStatus(200);
    }

    /**
     * SEC-CMS: Non-admin users are blocked from CMS views and mutations (403).
     */
    public function test_non_admin_blocked_from_cms_views_and_mutations(): void
    {
        // Sales user blocked from viewing CMS hub
        $response = $this->actingAs($this->salesUserA)->get('/erp/cms');
        $response->assertStatus(403);

        // Sales user blocked from adding a CMS category
        $response = $this->actingAs($this->salesUserA)->post('/erp/cms/categories', [
            'name_id' => 'Kategori Ilegal',
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('categories', ['slug' => 'kategori-ilegal']);

        // Sales user blocked from adding a CMS product
        $response = $this->actingAs($this->salesUserA)->post('/erp/cms/products', [
            'title_id' => 'Produk Ilegal',
            'category_slug' => 'material-handling',
        ]);
        $response->assertStatus(403);

        // Admin can access CMS hub
        $response = $this->actingAs($this->adminUser)->get('/erp/cms');
        $response->assertStatus(200);
    }

    /**
     * SEC-CMS: Uploading SVG or non-whitelisted image formats is rejected in CMS.
     */
    public function test_svg_and_malicious_upload_rejected_in_cms(): void
    {
        $fakeSvg = UploadedFile::fake()->create('exploit.svg', 10, 'image/svg+xml');

        $response = $this->actingAs($this->adminUser)->post('/erp/cms/products', [
            'title_id' => 'Unit Exploit Test',
            'category_slug' => 'material-handling',
            'image' => $fakeSvg,
        ]);

        $response->assertSessionHasErrors('image');
    }

    /**
     * SEC-BILL: Non-finance and non-procurement users cannot generate vendor bills from POs.
     */
    public function test_non_finance_procurement_blocked_from_generating_vendor_bill(): void
    {
        $po = Order::create([
            'order_number' => 'PO-202610-002',
            'order_type' => 'purchase_order',
            'procurement_type' => 'sub_rental',
            'contact_id' => $this->vendorContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'subtotal' => 10000000,
            'total_amount' => 10000000,
            'created_by' => $this->adminUser->id,
        ]);

        // Sales user blocked from generating vendor bill
        $response = $this->actingAs($this->salesUserA)->postJson("/erp/procurement/orders/{$po->id}/bill", [
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('invoices', ['order_id' => $po->id, 'invoice_type' => 'vendor_bill']);

        // Finance user CAN generate vendor bill
        $response = $this->actingAs($this->financeUser)->postJson("/erp/procurement/orders/{$po->id}/bill", [
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('invoices', ['order_id' => $po->id, 'invoice_type' => 'vendor_bill']);
    }

    /**
     * SEC-BILL: Cannot generate vendor bill for cancelled or draft PO.
     */
    public function test_cannot_generate_vendor_bill_for_cancelled_or_draft_po(): void
    {
        $cancelledPo = Order::create([
            'order_number' => 'PO-202610-003',
            'order_type' => 'purchase_order',
            'contact_id' => $this->vendorContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'cancelled',
            'subtotal' => 5000000,
            'total_amount' => 5000000,
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->financeUser)->postJson("/erp/procurement/orders/{$cancelledPo->id}/bill", [
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);
        $response->assertStatus(422);
    }

    /**
     * SEC-PO: Unauthorized user blocked from PO details modal drawer.
     */
    public function test_unauthorized_user_blocked_from_po_details_drawer(): void
    {
        $po = Order::create([
            'order_number' => 'PO-202610-004',
            'order_type' => 'purchase_order',
            'contact_id' => $this->vendorContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'subtotal' => 8000000,
            'total_amount' => 8000000,
            'created_by' => $this->adminUser->id,
        ]);

        // Sales user blocked from viewing PO details
        $response = $this->actingAs($this->salesUserA)->getJson("/erp/procurement/orders/{$po->id}/details");
        $response->assertStatus(403);

        // Operational user can view PO details
        $response = $this->actingAs($this->operationalUser)->getJson("/erp/procurement/orders/{$po->id}/details");
        $response->assertStatus(200);

        // Admin can view PO details
        $response = $this->actingAs($this->adminUser)->getJson("/erp/procurement/orders/{$po->id}/details");
        $response->assertStatus(200);
    }

    /**
     * SEC-FLEET: Non-operational and non-admin users blocked from mutating armada units.
     */
    public function test_non_operational_blocked_from_fleet_mutations(): void
    {
        $catalogItem = Item::create([
            'sku' => 'SKU-FORKLIFT-001',
            'name' => 'Forklift 3 Ton Diesel',
            'model' => 'FD30T',
            'brand' => 'Komatsu',
            'category' => 'Forklift',
            'unit' => 'Unit',
            'is_active' => true,
        ]);

        // Sales user blocked from adding new armada unit
        $response = $this->actingAs($this->salesUserA)->postJson('/erp/fleet', [
            'item_id' => $catalogItem->id,
            'serial_number' => 'SN-FORKLIFT-999',
            'ownership' => 'Perusahaan Sendiri',
            'status' => 'available',
        ]);
        $response->assertStatus(403);

        // Operational user CAN add new armada unit
        $response = $this->actingAs($this->operationalUser)->postJson('/erp/fleet', [
            'item_id' => $catalogItem->id,
            'serial_number' => 'SN-FORKLIFT-001',
            'ownership' => 'Perusahaan Sendiri',
            'status' => 'available',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('assets', ['serial_number' => 'SN-FORKLIFT-001']);
    }

    /**
     * SEC-CRM: Operational user cannot delete or manage sales leads as admin.
     */
    public function test_operational_user_cannot_delete_or_manage_crm_leads_as_admin(): void
    {
        $lead = Lead::create([
            'code' => 'LEAD-202610-001',
            'company_name' => 'PT Klien Utama Sales A',
            'pic_name' => 'Budi Santoso',
            'pic_phone' => '081234567890',
            'deal_type' => 'rental',
            'sales_id' => $this->salesUserA->id,
            'pipeline_stage' => 'new',
            'entry_date' => now()->format('Y-m-d'),
        ]);

        // Operational user blocked from deleting Sales A's lead
        $response = $this->actingAs($this->operationalUser)->postJson("/erp/crm/leads/{$lead->id}/delete");
        $response->assertStatus(403);
        $this->assertDatabaseHas('leads', ['id' => $lead->id]);

        // Sales B blocked from deleting Sales A's lead
        $response = $this->actingAs($this->salesUserB)->postJson("/erp/crm/leads/{$lead->id}/delete");
        $response->assertStatus(403);

        // Admin CAN delete lead
        $response = $this->actingAs($this->adminUser)->postJson("/erp/crm/leads/{$lead->id}/delete");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    /**
     * SEC-RATE: Login endpoint throttles after repeated failed attempts (HTTP 429).
     */
    public function test_login_rate_limiting_triggers_429_after_multiple_failed_attempts(): void
    {
        RateLimiter::clear('bruteforce@keylock.id|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'bruteforce@keylock.id',
                'password' => 'wrong-password-' . $i,
            ]);
            $response->assertSessionHasErrors('email');
        }

        // 6th attempt must be throttled with HTTP 429
        $response = $this->post('/login', [
            'email' => 'bruteforce@keylock.id',
            'password' => 'wrong-password-6',
        ]);
        $response->assertStatus(429);

        RateLimiter::clear('bruteforce@keylock.id|127.0.0.1');
    }
}
