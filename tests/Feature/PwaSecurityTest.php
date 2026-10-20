<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;

class PwaSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator', 'is_system' => true]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin PWA Tester',
            'email' => 'pwa-admin@keylock.id',
            'role' => 'admin',
            'usergrpid' => $adminRole->id,
            'is_active' => true,
        ]);
    }

    /**
     * 1. Validate manifest.webmanifest syntax, standalone display, and physical icon availability.
     */
    public function test_manifest_is_valid_json_and_has_required_pwa_fields(): void
    {
        $manifestPath = public_path('manifest.webmanifest');
        $this->assertFileExists($manifestPath);

        $content = file_get_contents($manifestPath);
        $data = json_decode($content, true);

        $this->assertIsArray($data, 'Manifest must be valid JSON.');
        $this->assertSame('Keylock Indonesia Mini-ERP', $data['name']);
        $this->assertSame('Keylock ERP', $data['short_name']);
        $this->assertSame('standalone', $data['display']);
        $this->assertSame('/', $data['start_url']);
        $this->assertSame('/', $data['scope']);
        $this->assertSame('#0F172A', $data['theme_color']);
        $this->assertNotEmpty($data['icons']);

        // Verify that every declared icon actually exists on disk
        foreach ($data['icons'] as $icon) {
            $iconPath = public_path(ltrim($icon['src'], '/'));
            $this->assertFileExists($iconPath, "Icon file {$icon['src']} must exist physically.");
        }
    }

    /**
     * 2. Validate that offline.html is static, self-contained, and free of sensitive/business data.
     */
    public function test_offline_fallback_html_is_clean_informative_and_free_of_sensitive_data(): void
    {
        $offlinePath = public_path('offline.html');
        $this->assertFileExists($offlinePath);

        $html = file_get_contents($offlinePath);

        // Informative offline notice
        $this->assertStringContainsString('Koneksi Internet Terputus', $html);
        $this->assertStringContainsString('window.location.reload()', $html);

        // Security check: must NOT contain sensitive operational data
        $this->assertStringNotContainsString('customer_id', $html);
        $this->assertStringNotContainsString('invoice_number', $html);
        $this->assertStringNotContainsString('total_amount', $html);
        $this->assertStringNotContainsString('balance_due', $html);
    }

    /**
     * 3. Validate Service Worker hardening rules: Network-Only for authenticated routes,
     *    no pre-caching of dynamic paths, and scoped cache cleanup.
     */
    public function test_service_worker_does_not_cache_authenticated_routes_or_use_cache_all(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);

        $swCode = file_get_contents($swPath);

        // Versioned cache name
        $this->assertStringContainsString('keylock-static-v2', $swCode);

        // Must NOT pre-cache /erp or /mobile in STATIC_ASSETS
        $this->assertMatchesRegularExpression('/STATIC_ASSETS\s*=\s*\[[^\]]*\]/s', $swCode, 'STATIC_ASSETS must be declared');
        preg_match('/STATIC_ASSETS\s*=\s*\[([^\]]*)\]/s', $swCode, $matches);
        $staticAssetsBlock = $matches[1] ?? '';
        $this->assertStringNotContainsString("'/erp'", $staticAssetsBlock);
        $this->assertStringNotContainsString("'/mobile'", $staticAssetsBlock);

        // Must enforce Network-Only for dynamic and authenticated routes
        $this->assertStringContainsString("url.pathname === '/erp'", $swCode);
        $this->assertStringContainsString("url.pathname.startsWith('/erp/')", $swCode);
        $this->assertStringContainsString("url.pathname === '/mobile'", $swCode);
        $this->assertStringContainsString("url.pathname.startsWith('/api/')", $swCode);

        // Must ONLY clean up caches starting with 'keylock-'
        $this->assertStringContainsString("key.startsWith('keylock-')", $swCode);

        // Must NOT unconditionally call skipWaiting() or clients.claim()
        $this->assertStringNotContainsString("self.skipWaiting();\n});", $swCode);
        $this->assertStringNotContainsString("self.clients.claim()", $swCode);
    }

    /**
     * 4. Verify start_url routing: Guest routes to /login, Authenticated routes to /erp.
     */
    public function test_root_url_routes_guests_to_login_and_authenticated_users_to_erp(): void
    {
        // Unauthenticated guest accessing /
        $guestResponse = $this->get('/');
        $guestResponse->assertRedirect(route('login'));

        // Authenticated user accessing /
        $authResponse = $this->actingAs($this->adminUser)->get('/');
        $authResponse->assertRedirect(route('erp.index'));
    }

    /**
     * 5. Verify views render PWA manifest link and service worker registration script.
     */
    public function test_login_and_erp_views_render_manifest_and_sw_registration(): void
    {
        // Login view
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200);
        $loginRes->assertSee('/manifest.webmanifest');
        $loginRes->assertSee("navigator.serviceWorker.register('/sw.js'", false);

        // ERP view
        $erpRes = $this->actingAs($this->adminUser)->get('/erp');
        $erpRes->assertStatus(200);
        $erpRes->assertSee('/manifest.webmanifest');
        $erpRes->assertSee("navigator.serviceWorker.register('/sw.js'", false);
    }
}
