<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Contact;
use App\Models\Item;

class OrderMobileCardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $salesUser;
    protected User $otherSalesUser;
    protected User $opsUser;
    protected User $financeUser;
    protected Contact $customerContact;
    protected Item $sampleItem;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator', 'is_system' => true]);
        $salesRole = Role::firstOrCreate(['name' => 'sales'], ['label' => 'Sales & Marketing', 'is_system' => false]);
        $opsRole = Role::firstOrCreate(['name' => 'operasional'], ['label' => 'Operasional', 'is_system' => false]);
        $financeRole = Role::firstOrCreate(['name' => 'finance'], ['label' => 'Keuangan', 'is_system' => false]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Order Tester',
            'email' => 'admin-order@keylock.id',
            'role' => 'admin',
            'usergrpid' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->salesUser = User::factory()->create([
            'name' => 'Sales A Tester',
            'email' => 'sales-a@keylock.id',
            'role' => 'sales',
            'usergrpid' => $salesRole->id,
            'is_active' => true,
        ]);

        $this->otherSalesUser = User::factory()->create([
            'name' => 'Sales B Tester',
            'email' => 'sales-b@keylock.id',
            'role' => 'sales',
            'usergrpid' => $salesRole->id,
            'is_active' => true,
        ]);

        $this->opsUser = User::factory()->create([
            'name' => 'Ops Order Tester',
            'email' => 'ops-order@keylock.id',
            'role' => 'operasional',
            'usergrpid' => $opsRole->id,
            'is_active' => true,
        ]);

        $this->financeUser = User::factory()->create([
            'name' => 'Finance Order Tester',
            'email' => 'finance-order@keylock.id',
            'role' => 'finance',
            'usergrpid' => $financeRole->id,
            'is_active' => true,
        ]);

        $this->customerContact = Contact::create([
            'type' => 'customer',
            'code' => 'CUST-001',
            'name' => 'PT Mitra Konstruksi Nusantara',
            'pic_name' => 'Bambang Sudarmono',
            'phone' => '081234567890',
            'email' => 'bambang@mitrakonstruksi.co.id',
            'is_active' => true,
        ]);

        $this->sampleItem = Item::create([
            'sku' => 'FL-3T-01',
            'name' => 'Forklift Diesel 3 Ton Toyota',
            'category' => 'Forklift',
            'rental_rate_monthly' => 25000000,
            'sale_price' => 320000000,
            'is_active' => true,
        ]);
    }

    /**
     * 1. Desktop table remains intact and is hidden on mobile screens (hidden md:block).
     */
    public function test_desktop_table_is_hidden_on_mobile_and_visible_on_desktop(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.orders.index'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Verify desktop table wrapper has hidden md:block
        $this->assertStringContainsString('hidden md:block overflow-x-auto', $content);

        // Verify desktop table markup exists
        $this->assertStringContainsString('<table class="w-full text-left text-xs whitespace-nowrap">', $content);
    }

    /**
     * 2. Mobile card container is present and hidden on desktop (block md:hidden).
     */
    public function test_mobile_card_container_is_rendered_and_hidden_on_desktop(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.orders.index'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Verify mobile cards container has block md:hidden
        $this->assertStringContainsString('block md:hidden', $content);
    }

    /**
     * 3. Quotation card renders actual document fields, status badge, items, and Rupiah format.
     */
    public function test_quotation_card_renders_actual_document_data(): void
    {
        $quotation = Order::create([
            'order_number' => 'QTN-202610-991',
            'order_type' => 'quotation',
            'quotation_type' => 'rental',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'draft',
            'subtotal' => 25000000,
            'tax_amount' => 2750000,
            'total_amount' => 27750000,
            'created_by' => $this->salesUser->id,
        ]);

        OrderItem::create([
            'order_id' => $quotation->id,
            'item_id' => $this->sampleItem->id,
            'description' => 'Forklift Diesel 3 Ton Toyota',
            'quantity' => 1,
            'unit' => 'Unit',
            'duration' => 1,
            'duration_unit' => 'Bulan',
            'unit_price' => 25000000,
            'line_total' => 25000000,
        ]);

        $response = $this->actingAs($this->salesUser)->get(route('erp.orders.index', ['type' => 'quotation']));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Document identifier & Customer
        $this->assertStringContainsString('QTN-202610-991', $content);
        $this->assertStringContainsString('PT Mitra Konstruksi Nusantara', $content);
        $this->assertStringContainsString('Bambang Sudarmono', $content);

        // Product description & Qty
        $this->assertStringContainsString('Forklift Diesel 3 Ton Toyota', $content);
        $this->assertStringContainsString('1 Unit', $content);

        // Rupiah Formatted Total
        $this->assertStringContainsString('Rp 27.750.000', $content);

        // Actions
        $this->assertStringContainsString('generateTransaction(' . $quotation->id, $content);
        $this->assertStringContainsString(route('erp.quotations.pdf', $quotation->id), $content);
    }

    /**
     * 4. Rental order card renders RO badge, customer, duration, and financial total.
     */
    public function test_rental_order_card_renders_actual_order_data(): void
    {
        $ro = Order::create([
            'order_number' => 'RO-202610-881',
            'order_type' => 'rental_order',
            'quotation_type' => 'rental',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'rental_start_date' => now()->format('Y-m-d'),
            'rental_duration' => 3,
            'rental_duration_unit' => 'month',
            'status' => 'in_progress',
            'subtotal' => 75000000,
            'tax_amount' => 8250000,
            'total_amount' => 83250000,
            'created_by' => $this->salesUser->id,
        ]);

        OrderItem::create([
            'order_id' => $ro->id,
            'item_id' => $this->sampleItem->id,
            'description' => 'Forklift Diesel 3 Ton Toyota (3 Bulan)',
            'quantity' => 1,
            'unit' => 'Unit',
            'duration' => 3,
            'duration_unit' => 'Bulan',
            'unit_price' => 25000000,
            'line_total' => 75000000,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('erp.orders.index', ['type' => 'transaksi']));
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertStringContainsString('RO-202610-881', $content);
        $this->assertStringContainsString('PT Mitra Konstruksi Nusantara', $content);
        $this->assertStringContainsString('3 Bulan', $content);
        $this->assertStringContainsString('Rp 83.250.000', $content);
    }

    /**
     * 5. Drawer endpoint returns correct document JSON details.
     */
    public function test_drawer_details_endpoint_returns_valid_document_data(): void
    {
        $quotation = Order::create([
            'order_number' => 'QTN-202610-771',
            'order_type' => 'quotation',
            'quotation_type' => 'rental',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'total_amount' => 15000000,
            'created_by' => $this->salesUser->id,
        ]);

        $response = $this->actingAs($this->salesUser)->getJson(route('erp.orders.drawer', $quotation->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'order' => [
                'id' => $quotation->id,
                'order_number' => 'QTN-202610-771',
                'contact' => [
                    'name' => 'PT Mitra Konstruksi Nusantara',
                ],
            ],
        ]);
    }

    /**
     * 6. Role-based actions on mobile cards:
     *    - Sales cannot convert another sales user's quotation.
     *    - Sales cannot see PO or Invoice creation buttons on transactions.
     *    - Ops can see DO creation button.
     *    - Finance can see Invoice creation button.
     */
    public function test_role_based_actions_are_properly_filtered_on_mobile_cards(): void
    {
        $order = Order::create([
            'order_number' => 'RO-202610-551',
            'order_type' => 'rental_order',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'in_progress',
            'subtotal' => 20000000,
            'total_amount' => 20000000,
            'created_by' => $this->salesUser->id,
        ]);

        // 6a. Sales viewing RO card: does NOT see Buat DO, Atur PO, or Buat Invoice in mobile cards
        $responseSales = $this->actingAs($this->salesUser)->get(route('erp.orders.index', ['type' => 'transaksi']));
        $mobileSales = explode('<!-- Pagination Bar', explode('<!-- MOBILE CARD VIEW', $responseSales->getContent())[1] ?? '')[0] ?? '';
        $this->assertStringNotContainsString('openDoModal(', $mobileSales);
        $this->assertStringNotContainsString('openAllocateModal(', $mobileSales);
        $this->assertStringNotContainsString('openGenerateInvoiceModal(', $mobileSales);

        // 6b. Ops viewing RO card: sees Buat DO and Atur PO, does NOT see Buat Invoice in mobile cards
        $responseOps = $this->actingAs($this->opsUser)->get(route('erp.orders.index', ['type' => 'transaksi']));
        $mobileOps = explode('<!-- Pagination Bar', explode('<!-- MOBILE CARD VIEW', $responseOps->getContent())[1] ?? '')[0] ?? '';
        $this->assertStringContainsString('openDoModal(', $mobileOps);
        $this->assertStringContainsString('openAllocateModal(', $mobileOps);
        $this->assertStringNotContainsString('openGenerateInvoiceModal(', $mobileOps);

        // 6c. Finance viewing RO card: sees Buat Invoice, does NOT see Buat DO or Atur PO in mobile cards
        $responseFinance = $this->actingAs($this->financeUser)->get(route('erp.orders.index', ['type' => 'transaksi']));
        $mobileFinance = explode('<!-- Pagination Bar', explode('<!-- MOBILE CARD VIEW', $responseFinance->getContent())[1] ?? '')[0] ?? '';
        $this->assertStringContainsString('openGenerateInvoiceModal(', $mobileFinance);
        $this->assertStringNotContainsString('openDoModal(', $mobileFinance);
        $this->assertStringNotContainsString('openAllocateModal(', $mobileFinance);
    }

    /**
     * 7. Backend strictly enforces authorization on conversion.
     */
    public function test_unauthorized_user_cannot_convert_another_sales_quotation(): void
    {
        $quotation = Order::create([
            'order_number' => 'QTN-202610-441',
            'order_type' => 'quotation',
            'quotation_type' => 'rental',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'subtotal' => 10000000,
            'total_amount' => 10000000,
            'created_by' => $this->salesUser->id,
        ]);

        OrderItem::create([
            'order_id' => $quotation->id,
            'item_id' => $this->sampleItem->id,
            'description' => 'Item to convert',
            'quantity' => 1,
            'unit_price' => 10000000,
            'line_total' => 10000000,
        ]);

        // Other sales attempts conversion -> Rejected (403)
        $responseOther = $this->actingAs($this->otherSalesUser)->postJson(route('erp.orders.convert', $quotation->id));
        $responseOther->assertStatus(403);

        // Owner sales converts own quotation -> Accepted (200)
        $responseOwner = $this->actingAs($this->salesUser)->postJson(route('erp.orders.convert', $quotation->id));
        $responseOwner->assertStatus(200);
        $responseOwner->assertJson(['success' => true]);

        // Verify child order was generated
        $this->assertDatabaseHas('orders', [
            'parent_id' => $quotation->id,
            'order_type' => 'rental_order',
        ]);
    }

    /**
     * 8. iOS Safe Area padding exists on slide-up drawer footer.
     */
    public function test_drawer_footer_includes_ios_safe_area_padding(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.orders.index'));
        $content = $response->getContent();

        $this->assertStringContainsString('env(safe-area-inset-bottom)', $content);
    }

    /**
     * 9. Phase 4D.1B: Mobile header, filter chips, Total Nilai box, and action buttons.
     */
    public function test_phase_4d1b_mobile_redesign_header_chips_and_card_elements(): void
    {
        $ro = Order::create([
            'order_number' => 'RO-202610-999',
            'order_type' => 'rental_order',
            'contact_id' => $this->customerContact->id,
            'order_date' => now()->format('Y-m-d'),
            'rental_duration' => 2,
            'rental_duration_unit' => 'month',
            'status' => 'in_progress',
            'subtotal' => 50000000,
            'total_amount' => 55500000,
            'created_by' => $this->salesUser->id,
        ]);

        OrderItem::create([
            'order_id' => $ro->id,
            'item_id' => $this->sampleItem->id,
            'description' => 'Boom Lift 16M',
            'quantity' => 1,
            'unit' => 'Unit',
            'duration' => 2,
            'duration_unit' => 'Bulan',
            'unit_price' => 25000000,
            'line_total' => 50000000,
        ]);

        $response = $this->actingAs($this->opsUser)->get(route('erp.orders.index', ['type' => 'transaksi']));
        $response->assertStatus(200);

        $content = $response->getContent();

        // 9a. Mobile Header elements
        $this->assertStringContainsString('Kembali ke Dashboard', $content);
        $this->assertStringContainsString('Penawaran &amp; Order', $content);
        $this->assertStringContainsString('Kelola quotation, sales order, dan rental order', $content);

        // 9b. Mobile Tabs: Only Penawaran Harga & Transaksi, Tab Semua removed from mobile
        $this->assertStringContainsString('id="mobile-order-tabs"', $content);
        $this->assertStringContainsString('Penawaran Harga', $content);
        $this->assertStringContainsString('Transaksi', $content);

        // 9c. Card Total Nilai box and Lihat Detail
        $this->assertStringContainsString('Total Nilai', $content);
        $this->assertStringContainsString('Lihat Detail', $content);
        $this->assertStringContainsString('Rp 55.500.000', $content);

        // 9d. Action Buttons: Cetak PDF & Primary Action Buat DO for Ops
        $this->assertStringContainsString('Cetak PDF', $content);
        $this->assertStringContainsString('Buat DO', $content);
    }

    /**
     * 10. Phase 4D.1C: Mobile tabs only show Penawaran Harga and Transaksi, and limit to 10 records.
     */
    public function test_phase_4d1c_mobile_tabs_only_two_and_limits_to_10_records(): void
    {
        // Seed 14 quotations
        for ($i = 1; $i <= 14; $i++) {
            Order::create([
                'order_number' => sprintf('QTN-4D1C-%03d', $i),
                'order_type' => 'quotation',
                'quotation_type' => 'rental',
                'contact_id' => $this->customerContact->id,
                'order_date' => now()->subDays($i)->format('Y-m-d'),
                'valid_until' => now()->addDays(14)->format('Y-m-d'),
                'status' => 'draft',
                'subtotal' => 10000000 * $i,
                'total_amount' => 11100000 * $i,
                'created_by' => $this->salesUser->id,
            ]);
        }

        // Test with mobile user-agent
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1'
        ])->actingAs($this->salesUser)->get(route('erp.orders.index', ['type' => 'quotation']));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Check tabs container: no "Semua" pill inside #mobile-order-tabs
        $pos = strpos($content, 'id="mobile-order-tabs"');
        $this->assertNotFalse($pos, 'Mobile order tabs container should be present');
        $tabsHtml = substr($content, $pos, 2500);
        $this->assertStringNotContainsString('Semua (', $tabsHtml);
        $this->assertStringContainsString('Penawaran Harga', $tabsHtml);
        $this->assertStringContainsString('Transaksi', $tabsHtml);

        // Verify page 1 contains at most 10 items
        $ordersPaginator = $response->viewData('orders');
        $this->assertNotNull($ordersPaginator);
        $this->assertEquals(10, $ordersPaginator->perPage());
        $this->assertLessThanOrEqual(10, $ordersPaginator->count());
        $this->assertTrue($ordersPaginator->hasPages());

        // Verify page 2 is accessible and returns remaining items
        $responsePage2 = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1'
        ])->actingAs($this->salesUser)->get(route('erp.orders.index', ['type' => 'quotation', 'page' => 2]));

        $responsePage2->assertStatus(200);
        $ordersPaginatorP2 = $responsePage2->viewData('orders');
        $this->assertEquals(2, $ordersPaginatorP2->currentPage());
        $this->assertGreaterThan(0, $ordersPaginatorP2->count());
    }

    /**
     * 11. Phase 4D.1C: Mobile dashboard has 2-column layout, hero banner, and no emojis.
     */
    public function test_phase_4d1c_mobile_dashboard_redesign_and_no_emojis(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.index'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Mobile Hero Banner presence
        $this->assertStringContainsString('Accurate, Efficient, and Innovative.', $content);
        $this->assertStringContainsString('Kelola operasional rental armada, lacak pengiriman, dan kendalikan invoicing secara efisien.', $content);
        $this->assertStringContainsString('block md:hidden', $content);

        // 2-column KPI grid on mobile
        $this->assertStringContainsString('grid grid-cols-2 lg:grid-cols-4', $content);

        // Dynamic KPI metrics rendered
        $this->assertStringContainsString('Total Pendapatan', $content);
        $this->assertStringContainsString('Total Order', $content);
        $this->assertStringContainsString('Piutang Customer', $content);
        $this->assertStringContainsString('Utang Vendor', $content);

        // Verify sales dashboard has 2 columns
        $salesResp = $this->actingAs($this->salesUser)->get(route('erp.index'));
        $salesResp->assertStatus(200);
        $salesContent = $salesResp->getContent();
        $this->assertStringContainsString('Accurate, Efficient, and Innovative.', $salesContent);
        $this->assertStringContainsString('Target Penjualan', $salesContent);
        $this->assertStringContainsString('grid grid-cols-2 lg:grid-cols-4', $salesContent);

        // Verify owner dashboard has 2 columns
        $ownerRole = Role::firstOrCreate(['name' => 'owner'], ['label' => 'Founder / Owner', 'is_system' => false]);
        $ownerUser = User::factory()->create([
            'name' => 'Owner Tester',
            'email' => 'owner-test-' . uniqid() . '@keylock.id',
            'role' => 'owner',
            'usergrpid' => $ownerRole->id,
            'is_active' => true,
        ]);
        $ownerResp = $this->actingAs($ownerUser)->get(route('erp.index'));
        $ownerResp->assertStatus(200);
        $ownerContent = $ownerResp->getContent();
        $this->assertStringContainsString('Accurate, Efficient, and Innovative.', $ownerContent);
        $this->assertStringContainsString('grid grid-cols-2 lg:grid-cols-5', $ownerContent);
        $this->assertStringContainsString('Margin Kotor', $ownerContent);
        $this->assertStringContainsString('Arus Kas', $ownerContent);

        // Verify no raw emojis used in desktop blade
        $desktopBladeFile = resource_path('views/erp/desktop.blade.php');
        $this->assertFileExists($desktopBladeFile);
        $desktopContent = file_get_contents($desktopBladeFile);

        $hasEmoji = preg_match('/[\x{1F300}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $desktopContent);
        $this->assertSame(0, $hasEmoji, 'Desktop blade should not contain raw emoji characters.');
    }
}

