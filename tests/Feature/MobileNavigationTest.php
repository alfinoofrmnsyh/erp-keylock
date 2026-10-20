<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;

class MobileNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $salesUser;
    protected User $opsUser;
    protected User $financeUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles if necessary
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator', 'is_system' => true]);
        $salesRole = Role::firstOrCreate(['name' => 'sales'], ['label' => 'Sales & Marketing', 'is_system' => false]);
        $opsRole = Role::firstOrCreate(['name' => 'operasional'], ['label' => 'Operasional', 'is_system' => false]);
        $financeRole = Role::firstOrCreate(['name' => 'finance'], ['label' => 'Keuangan', 'is_system' => false]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Mobile Tester',
            'email' => 'admin-mobile@keylock.id',
            'role' => 'admin',
            'usergrpid' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->salesUser = User::factory()->create([
            'name' => 'Sales Mobile Tester',
            'email' => 'sales-mobile@keylock.id',
            'role' => 'sales',
            'usergrpid' => $salesRole->id,
            'is_active' => true,
        ]);

        $this->opsUser = User::factory()->create([
            'name' => 'Ops Mobile Tester',
            'email' => 'ops-mobile@keylock.id',
            'role' => 'operasional',
            'usergrpid' => $opsRole->id,
            'is_active' => true,
        ]);

        $this->financeUser = User::factory()->create([
            'name' => 'Finance Mobile Tester',
            'email' => 'finance-mobile@keylock.id',
            'role' => 'finance',
            'usergrpid' => $financeRole->id,
            'is_active' => true,
        ]);
    }

    /**
     * 1. Desktop sidebar has responsive classes (hidden md:flex) and mobile dock is block md:hidden.
     */
    public function test_desktop_sidebar_is_hidden_on_mobile_and_dock_is_hidden_on_desktop(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.index'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Desktop sidebar must have hidden md:flex
        $this->assertStringContainsString('aside class="hidden md:flex w-64', $content);

        // Mobile bottom dock must be present and hidden on desktop (block md:hidden)
        $this->assertStringContainsString('block md:hidden', $content);
        $this->assertStringContainsString('fixed bottom-0', $content);

        // Safe area padding for iOS
        $this->assertStringContainsString('env(safe-area-inset-bottom)', $content);
    }

    /**
     * 2. Content container has bottom padding to prevent bottom dock overlap on mobile.
     */
    public function test_content_container_has_adequate_bottom_padding_on_mobile(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.index'));
        $response->assertStatus(200);

        // Content wrapper must have pb-28 md:pb-8
        $response->assertSee('pb-28 md:pb-8', false);
    }

    /**
     * 3. Role: Sales gets Beranda, CRM, Quick Actions (+), Penawaran, and Lainnya (...).
     */
    public function test_mobile_dock_renders_correct_tabs_for_sales(): void
    {
        $response = $this->actingAs($this->salesUser)->get(route('erp.index'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Sales primary tabs
        $this->assertStringContainsString(route('erp.crm.index'), $content);
        $this->assertStringContainsString(route('erp.orders.index'), $content);

        // Center Quick Action button
        $this->assertStringContainsString('toggleMobileQuickActions()', $content);
        $this->assertStringContainsString('toggleMobileMoreMenu()', $content);

        // Sales quick action includes Quotation and Lead
        $this->assertStringContainsString('Buat Penawaran Harga (Quotation)', $content);
        $this->assertStringContainsString('Input Lead Prospek Baru (CRM)', $content);

        // Sales quick action does NOT include Payment
        $this->assertStringNotContainsString('Catat Pembayaran Masuk (Payment)', $content);
    }

    /**
     * 4. Role: Operasional gets Beranda, Armada, Quick Actions (+), Orders, and Lainnya (...).
     */
    public function test_mobile_dock_renders_correct_tabs_for_operational(): void
    {
        $response = $this->actingAs($this->opsUser)->get(route('erp.index'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Ops primary tabs
        $this->assertStringContainsString(route('erp.fleet.index'), $content);
        $this->assertStringContainsString(route('erp.orders.index'), $content);

        // Ops quick actions: Servis and PO
        $this->assertStringContainsString('Catat Servis Armada & Hour Meter', $content);
        $this->assertStringContainsString('Buat PO Pengadaan / Vendor', $content);

        // Ops does NOT see Lead CRM creation in quick actions
        $this->assertStringNotContainsString('Input Lead Prospek Baru (CRM)', $content);
    }

    /**
     * 5. Role: Finance gets Beranda, Invoice, Quick Actions (+), Transaksi, and Lainnya (...).
     */
    public function test_mobile_dock_renders_correct_tabs_for_finance(): void
    {
        $response = $this->actingAs($this->financeUser)->get(route('erp.index'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Finance primary tabs
        $this->assertStringContainsString(route('erp.finance.index'), $content);
        $this->assertStringContainsString(route('erp.orders.index'), $content);

        // Finance quick actions: Catat Pembayaran
        $this->assertStringContainsString('Catat Pembayaran Masuk (Payment)', $content);

        // Finance does NOT see PO or Servis Armada in quick actions
        $this->assertStringNotContainsString('Catat Servis Armada &amp; Hour Meter', $content);
        $this->assertStringNotContainsString('Buat PO Pengadaan / Vendor', $content);
    }

    /**
     * 6. Role: Admin gets full access and sees administration items in more menu.
     */
    public function test_mobile_dock_renders_for_admin_with_admin_menu(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.index'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Admin primary tabs: Orders and Fleet
        $this->assertStringContainsString(route('erp.orders.index'), $content);
        $this->assertStringContainsString(route('erp.fleet.index'), $content);

        // Admin more menu contains Users, Menus, Configs, CMS
        $this->assertStringContainsString(route('erp.users.index'), $content);
        $this->assertStringContainsString(route('erp.menus.index'), $content);
        $this->assertStringContainsString(route('erp.configs.index'), $content);
        $this->assertStringContainsString(route('erp.cms.index'), $content);
    }

    /**
     * 7. Active tab highlights correctly based on request URL.
     */
    public function test_active_tab_is_highlighted_based_on_current_route(): void
    {
        // 7a. On /erp, Beranda should be aria-current="page"
        $responseHome = $this->actingAs($this->adminUser)->get(route('erp.index'));
        $contentHome = $responseHome->getContent();
        $this->assertMatchesRegularExpression('/href="[^"]*\/erp"[^>]*aria-current="page"/i', $contentHome);

        // 7b. On /erp/orders, Transaksi should be aria-current="page"
        $responseOrders = $this->actingAs($this->adminUser)->get(route('erp.orders.index'));
        $contentOrders = $responseOrders->getContent();
        $this->assertMatchesRegularExpression('/href="[^"]*\/erp\/orders"[^>]*aria-current="page"/i', $contentOrders);

        // 7c. On /erp/fleet, Armada should be aria-current="page"
        $responseFleet = $this->actingAs($this->adminUser)->get(route('erp.fleet.index'));
        $contentFleet = $responseFleet->getContent();
        $this->assertMatchesRegularExpression('/href="[^"]*\/erp\/fleet"[^>]*aria-current="page"/i', $contentFleet);
    }

    /**
     * 8. Logout form in mobile drawer uses strict POST and CSRF token.
     */
    public function test_mobile_drawer_logout_uses_secure_post_and_csrf(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.index'));
        $content = $response->getContent();

        // Verify form method is POST
        $this->assertMatchesRegularExpression('/<form[^>]+method="POST"[^>]+action="[^"]*logout"[^>]*>/i', $content);

        // Verify CSRF input token is present inside the form
        $this->assertMatchesRegularExpression('/<input[^>]+type="hidden"[^>]+name="_token"/i', $content);
    }

    /**
     * 9. Quick actions do not trigger auto-mutations on click.
     */
    public function test_quick_actions_do_not_perform_immediate_mutations(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.index'));
        $content = $response->getContent();

        // Quick action 1: Buat Quotation opens modal via modalQuotationOpen = true
        $this->assertStringContainsString('modalQuotationOpen = true', $content);

        // Other quick actions are standard <a> navigation links to forms/indexes
        $this->assertMatchesRegularExpression('/<a[^>]+href="[^"]*\/erp\/crm[^"]*"[^>]*>/i', $content);
        $this->assertMatchesRegularExpression('/<a[^>]+href="[^"]*\/erp\/fleet[^"]*"[^>]*>/i', $content);
        $this->assertMatchesRegularExpression('/<a[^>]+href="[^"]*\/erp\/procurement[^"]*"[^>]*>/i', $content);
        $this->assertMatchesRegularExpression('/<a[^>]+href="[^"]*\/erp\/finance[^"]*"[^>]*>/i', $content);
    }

    /**
     * 10. Direct backend access to admin routes is rejected for non-admin roles.
     */
    public function test_direct_access_to_unauthorized_routes_is_rejected_by_backend(): void
    {
        // Sales cannot access user management
        $responseSalesUsers = $this->actingAs($this->salesUser)->get(route('erp.users.index'));
        $this->assertTrue(
            in_array($responseSalesUsers->status(), [403, 302]),
            "Sales user must be denied access to user management, got {$responseSalesUsers->status()}"
        );

        // Ops cannot access user management
        $responseOpsUsers = $this->actingAs($this->opsUser)->get(route('erp.users.index'));
        $this->assertTrue(
            in_array($responseOpsUsers->status(), [403, 302]),
            "Ops user must be denied access to user management, got {$responseOpsUsers->status()}"
        );
    }

    /**
     * 11. Accessibility attributes (ARIA, roles) are present in the mobile dock and drawers.
     */
    public function test_accessibility_roles_and_labels_are_present(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('erp.index'));
        $content = $response->getContent();

        // Nav role and label
        $this->assertStringContainsString('role="navigation"', $content);
        $this->assertStringContainsString('aria-label="Navigasi Bawah Mobile"', $content);

        // Bottom sheet dialog roles
        $this->assertStringContainsString('role="dialog"', $content);
        $this->assertStringContainsString('aria-modal="true"', $content);
        $this->assertStringContainsString('aria-labelledby="quick-actions-title"', $content);
        $this->assertStringContainsString('aria-labelledby="more-menu-title"', $content);

        // Center button ARIA
        $this->assertStringContainsString('aria-label="Aksi Cepat Transaksi"', $content);
        $this->assertStringContainsString(':aria-expanded="mobileQuickActionsOpen"', $content);
    }
}
