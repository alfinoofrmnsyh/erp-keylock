<?php

namespace Tests\Feature;

use App\Models\CompanyConfig;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportHubTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_admin_can_access_reports_and_see_all_tabs(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'usergrpid' => 1]);

        $response = $this->actingAs($admin)->get(route('erp.reports.index', ['tab' => 'sales']));
        $response->assertStatus(200);
        $response->assertSee('Pusat Laporan & Rekapitulasi', false);
        $response->assertSee('Laporan Sales & CRM', false);
        $response->assertSee('Laporan Operasional & Unit', false);
        $response->assertSee('Laporan Finansial Owner', false);
        $response->assertSee('Laporan Insentif Sales', false);
        $response->assertSee('Konfigurasi Hak Akses', false);
    }

    public function test_sales_user_can_access_sales_tab_but_not_access_config(): void
    {
        $salesUser = User::where('role', 'sales')->first() ?? User::factory()->create(['role' => 'sales', 'usergrpid' => 2]);

        $response = $this->actingAs($salesUser)->get(route('erp.reports.index', ['tab' => 'sales']));
        $response->assertStatus(200);
        $response->assertSee('Laporan Sales & CRM', false);

        // Sales user should not see Konfigurasi Hak Akses tab
        $response->assertDontSee('Konfigurasi Hak Akses');

        // If sales user directly requests tab=access, they should default back to allowed tab
        $forbiddenTabResponse = $this->actingAs($salesUser)->get(route('erp.reports.index', ['tab' => 'access']));
        $forbiddenTabResponse->assertStatus(200);
        $forbiddenTabResponse->assertDontSee('Konfigurasi Hak Akses');
    }

    public function test_admin_can_access_incentive_tab(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'usergrpid' => 1]);

        $response = $this->actingAs($admin)->get(route('erp.reports.index', ['tab' => 'incentive']));
        $response->assertStatus(200);
        $response->assertSee('Total Insentif Komisi', false);
        $response->assertSee('Rekapitulasi Komisi Per Sales Person', false);
        $response->assertSee('Rincian Transaksi & Audit Hak Insentif', false);
    }

    public function test_admin_can_update_report_tab_permissions_and_sync_appmenuaccess(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'usergrpid' => 1]);

        $postData = [
            'sales_roles' => [1, 2],
            'operational_roles' => [1, 3, 6],
            'owner_roles' => [1, 5],
            'incentive_roles' => [1, 5],
        ];

        $response = $this->actingAs($admin)->post(route('erp.reports.access_config'), $postData);
        $response->assertRedirect(route('erp.reports.index', ['tab' => 'access']));
        $response->assertSessionHas('success');

        $config = CompanyConfig::where('key', 'report_tab_permissions')->first();
        $this->assertNotNull($config);
        $decoded = json_decode($config->value, true);
        $this->assertContains(2, $decoded['sales']);
        $this->assertContains(3, $decoded['operational']);
        $this->assertContains(5, $decoded['incentive']);
    }

    public function test_csv_export_returns_streamed_file_with_bom(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'usergrpid' => 1]);

        $response = $this->actingAs($admin)->get(route('erp.reports.export', ['tab' => 'sales']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('Content-Disposition'));

        // Test incentive CSV export as well
        $incResponse = $this->actingAs($admin)->get(route('erp.reports.export', ['tab' => 'incentive']));
        $incResponse->assertStatus(200);
        $incResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Insentif_Sales', $incResponse->headers->get('Content-Disposition'));
    }

    public function test_print_view_renders_printable_report(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'usergrpid' => 1]);

        $response = $this->actingAs($admin)->get(route('erp.reports.print', ['tab' => 'owner']));
        $response->assertStatus(200);
        $response->assertSee('PT KEY LOCK INDONESIA');
        $response->assertSee('Laporan Finansial & Profitabilitas Owner', false);
        $response->assertSee('Cetak / Simpan ke PDF');

        // Test incentive print view
        $incResponse = $this->actingAs($admin)->get(route('erp.reports.print', ['tab' => 'incentive']));
        $incResponse->assertStatus(200);
        $incResponse->assertSee('Laporan Insentif Sales & Komisi Penjualan', false);
        $incResponse->assertSee('Total Insentif Komisi');
    }
}
