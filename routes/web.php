<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB; 

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Erp\ErpController;
use App\Http\Controllers\Erp\CrmController;
use App\Http\Controllers\Erp\ProcurementController;
use App\Http\Controllers\Erp\FinanceController;
use App\Http\Controllers\Erp\FleetController;
use App\Http\Controllers\Erp\ConfigController;
use App\Http\Controllers\Erp\CmsController;
use App\Http\Controllers\Erp\ReportController;

// 1. Root Entry - Directs to Login if unauthenticated, or to Mini-ERP if logged in
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('erp.index');
    }
    return redirect()->route('login');
});

// 2. Authentication Routes (Breeze / Session Auth)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// 3. Mini-ERP Portal (Protected by Auth & Role-ready)
Route::middleware(['auth'])->group(function () {
    Route::get('/mobile', [ErpController::class, 'mobile'])->name('mobile');

    Route::prefix('erp')->name('erp.')->group(function () {
        Route::get('/', [ErpController::class, 'index'])->name('index');
        Route::get('/master-data', [ErpController::class, 'masterData'])->name('master_data.index');
        Route::post('/master-data/contact', [ErpController::class, 'storeMasterContact'])->name('master_data.contact.store');
        Route::post('/master-data/contact/{id}/update', [ErpController::class, 'updateMasterContact'])->name('master_data.contact.update');
        Route::post('/master-data/contact/{id}/toggle-status', [ErpController::class, 'toggleContactStatus'])->name('master_data.contact.toggle_status');
        Route::post('/master-data/product', [ErpController::class, 'storeMasterItem'])->name('master_data.product.store');
        Route::post('/master-data/asset', [ErpController::class, 'storeMasterAsset'])->name('master_data.asset.store');
        Route::get('/orders', [ErpController::class, 'orders'])->name('orders.index');
        
        // User & Role Management (Admin only)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/users', [ErpController::class, 'userManagement'])->name('users.index');
            Route::post('/users', [ErpController::class, 'storeUser'])->name('users.store');
            Route::post('/users/{id}/update', [ErpController::class, 'updateUser'])->name('users.update');
            Route::post('/users/{id}/toggle-status', [ErpController::class, 'toggleUserStatus'])->name('users.toggle_status');
            Route::post('/roles', [ErpController::class, 'storeRole'])->name('roles.store');
            Route::post('/roles/{id}/update', [ErpController::class, 'updateRole'])->name('roles.update');
            Route::post('/roles/{id}/delete', [ErpController::class, 'deleteRole'])->name('roles.delete');

            // Menu & Access Management (Admin only)
            Route::get('/system-menus', [ErpController::class, 'systemMenus'])->name('menus.index');
            Route::post('/system-menus', [ErpController::class, 'storeSystemMenu'])->name('menus.store');
            Route::post('/system-menus/{id}/update', [ErpController::class, 'updateSystemMenu'])->name('menus.update');
            Route::post('/system-menus/{id}/delete', [ErpController::class, 'deleteSystemMenu'])->name('menus.delete');
            Route::post('/system-menus/access/toggle', [ErpController::class, 'toggleMenuAccess'])->name('menus.access.toggle');
            Route::post('/system-menus/access/sync', [ErpController::class, 'syncMenuAccess'])->name('menus.access.sync');
            Route::post('/system-menus/access/delete', [ErpController::class, 'deleteMenuAccess'])->name('menus.access.delete');
        });
        Route::post('/quotations', [ErpController::class, 'storeQuotation'])->name('quotations.store');
        Route::post('/quotations/{id}/update', [ErpController::class, 'updateQuotation'])->name('quotations.update');
        Route::post('/orders/{id}/convert', [ErpController::class, 'convertOrder'])->name('orders.convert');
        Route::post('/orders/{id}/generate-do', [ErpController::class, 'generateDelivery'])->name('orders.generate_do');
        Route::post('/orders/{id}/generate-invoice', [ErpController::class, 'generateInvoiceFromOrder'])->name('orders.generate_invoice');
        Route::post('/orders/{id}/cancel', [ErpController::class, 'cancelOrder'])->name('orders.cancel');
        Route::post('/orders/{id}/complete', [ErpController::class, 'completeOrder'])->name('orders.complete');
        Route::get('/orders/{id}/drawer', [ErpController::class, 'getDrawerDetails'])->name('orders.drawer');
        Route::get('/quotations/{id}/pdf', [ErpController::class, 'quotationPdf'])->name('quotations.pdf');
        Route::get('/deliveries/{id}/pdf', [ErpController::class, 'deliveryPdf'])->name('deliveries.pdf');
        Route::get('/invoices/{id}/pdf', [FinanceController::class, 'invoicePdf'])->name('invoices.pdf');

        // 5. CRM Module (Ringkasan, Pipeline Kanban, Semua Leads, Aktivitas & Follow-up)
        Route::get('/crm', [CrmController::class, 'index'])->name('crm.index');
        Route::post('/crm/leads', [CrmController::class, 'storeLead'])->name('crm.leads.store');
        Route::post('/crm/leads/{id}/update', [CrmController::class, 'updateLead'])->name('crm.leads.update');
        Route::post('/crm/leads/{id}/stage', [CrmController::class, 'updateStage'])->name('crm.leads.stage');
        Route::post('/crm/leads/{id}/activities', [CrmController::class, 'storeActivity'])->name('crm.leads.activity.store');
        Route::post('/crm/leads/{id}/follow-ups', [CrmController::class, 'storeFollowUp'])->name('crm.leads.follow_up.store');
        Route::post('/crm/follow-ups/{id}/complete', [CrmController::class, 'completeFollowUp'])->name('crm.follow_up.complete');
        Route::get('/crm/leads/{id}/details', [CrmController::class, 'getLeadDetails'])->name('crm.leads.details');
        Route::post('/crm/leads/{id}/prepare-quotation', [CrmController::class, 'prepareQuotation'])->name('crm.leads.prepare_quotation');
        Route::post('/crm/leads/{id}/convert-customer', [CrmController::class, 'convertToCustomer'])->name('crm.leads.convert_customer');
        Route::post('/crm/leads/{id}/delete', [CrmController::class, 'deleteLead'])->name('crm.leads.delete');

        // 6. Procurement & Vendor Module
        Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
        Route::middleware(['role:admin,procurement,operasional'])->group(function () {
            Route::post('/procurement/orders', [ProcurementController::class, 'store'])->name('procurement.store');
            Route::post('/procurement/allocate', [ProcurementController::class, 'allocateToOrder'])->name('procurement.allocate');
            Route::post('/procurement/orders/{id}/cancel', [ProcurementController::class, 'cancelOrder'])->name('procurement.cancel');
        });
        Route::post('/procurement/orders/{id}/bill', [ProcurementController::class, 'generateVendorBill'])
            ->name('procurement.generate_bill')
            ->middleware('role:admin,finance,procurement');
        Route::get('/procurement/orders/{id}/details', [ProcurementController::class, 'getDetails'])->name('procurement.details');
        Route::get('/procurement/orders/{id}/pdf', [ProcurementController::class, 'poPdf'])->name('procurement.pdf');

        // 7. Finance & Invoicing Module
        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::middleware(['role:admin,finance'])->group(function () {
            Route::post('/finance/invoices', [FinanceController::class, 'storeInvoice'])->name('finance.invoices.store');
            Route::post('/finance/bills', [FinanceController::class, 'storeVendorBill'])->name('finance.bills.store');
            Route::post('/finance/payments', [FinanceController::class, 'storePayment'])->name('finance.payments.store');
        });
        Route::get('/finance/invoices/{id}/pdf', [FinanceController::class, 'invoicePdf'])->name('finance.invoices.pdf');
        Route::get('/finance/invoices/{id}/details', [FinanceController::class, 'getDetails'])->name('finance.invoices.details');

        // 8. Fleet & Armada Module
        Route::get('/fleet', [FleetController::class, 'index'])->name('fleet.index');
        Route::get('/fleet/{id}/details', [FleetController::class, 'getDetails'])->name('fleet.details');
        Route::middleware(['role:admin,operasional,admin_op'])->group(function () {
            Route::post('/fleet', [FleetController::class, 'store'])->name('fleet.store');
            Route::post('/fleet/{id}/update', [FleetController::class, 'update'])->name('fleet.update');
            Route::post('/fleet/{id}/service', [FleetController::class, 'storeService'])->name('fleet.service.store');
            Route::post('/fleet/{id}/return', [FleetController::class, 'returnUnit'])->name('fleet.return');
            Route::post('/fleet/{id}/assign', [FleetController::class, 'assignOrder'])->name('fleet.assign');
        });

        // 9. Master Config & Dropdowns Module
        Route::get('/system-configs', [ConfigController::class, 'index'])->name('configs.index');
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/system-configs', [ConfigController::class, 'store'])->name('configs.store');
            Route::post('/system-configs/{id}/update', [ConfigController::class, 'update'])->name('configs.update');
            Route::post('/system-configs/{id}/toggle-status', [ConfigController::class, 'toggleStatus'])->name('configs.toggle_status');
            Route::post('/system-configs/{id}/delete', [ConfigController::class, 'destroy'])->name('configs.destroy');
            Route::post('/system-configs/company', [ConfigController::class, 'updateCompany'])->name('configs.company.update');
        });

        // 10. Konten & CMS Website Module (Admin only)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/cms', [CmsController::class, 'index'])->name('cms.index');
            Route::get('/categories', [CmsController::class, 'categories'])->name('cms.categories');
            Route::get('/products', [CmsController::class, 'products'])->name('cms.products');
            Route::get('/galleries', [CmsController::class, 'galleries'])->name('cms.galleries');
            Route::get('/partners', [CmsController::class, 'partners'])->name('cms.partners');
            Route::get('/testimonials', [CmsController::class, 'testimonials'])->name('cms.testimonials');
            Route::get('/manage-about-page', [CmsController::class, 'about'])->name('cms.about');
            Route::get('/cms-contact', [CmsController::class, 'contact'])->name('cms.contact');

            // CMS Actions
            Route::post('/cms/categories', [CmsController::class, 'storeCategory'])->name('cms.categories.store');
            Route::post('/cms/categories/{id}/update', [CmsController::class, 'updateCategory'])->name('cms.categories.update');
            Route::post('/cms/categories/{id}/delete', [CmsController::class, 'deleteCategory'])->name('cms.categories.delete');

            Route::post('/cms/products', [CmsController::class, 'storeProduct'])->name('cms.products.store');
            Route::post('/cms/products/{id}/update', [CmsController::class, 'updateProduct'])->name('cms.products.update');
            Route::post('/cms/products/{id}/delete', [CmsController::class, 'deleteProduct'])->name('cms.products.delete');

            Route::post('/cms/partners', [CmsController::class, 'storePartner'])->name('cms.partners.store');
            Route::post('/cms/partners/{id}/update', [CmsController::class, 'updatePartner'])->name('cms.partners.update');
            Route::post('/cms/partners/{id}/delete', [CmsController::class, 'deletePartner'])->name('cms.partners.delete');

            Route::post('/cms/galleries', [CmsController::class, 'storeGallery'])->name('cms.galleries.store');
            Route::post('/cms/galleries/{id}/update', [CmsController::class, 'updateGallery'])->name('cms.galleries.update');
            Route::post('/cms/galleries/{id}/toggle-status', [CmsController::class, 'toggleGalleryStatus'])->name('cms.galleries.toggle_status');
            Route::post('/cms/galleries/{id}/delete', [CmsController::class, 'deleteGallery'])->name('cms.galleries.delete');

            Route::post('/cms/testimonials', [CmsController::class, 'storeTestimonial'])->name('cms.testimonials.store');
            Route::post('/cms/testimonials/{id}/update', [CmsController::class, 'updateTestimonial'])->name('cms.testimonials.update');
            Route::post('/cms/testimonials/{id}/delete', [CmsController::class, 'deleteTestimonial'])->name('cms.testimonials.delete');

            Route::post('/cms/about', [CmsController::class, 'updateAboutPage'])->name('cms.about.update');
            Route::post('/cms/contact', [CmsController::class, 'updateContact'])->name('cms.contact.update');
        });

        // 11. Unified Reports & Rekapitulasi Module
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
        Route::post('/reports/access-config', [ReportController::class, 'updateAccessConfig'])
            ->name('reports.access_config')
            ->middleware('role:admin');

        // Compatibility redirect for /erp/contacts -> /erp/crm
        Route::get('/contacts', function () {
            return redirect()->route('erp.crm.index');
        })->name('contacts.index');
    });

    // Alias for Filament navigation compatibility
    Route::get('/erp/orders-hub', function () {
        return redirect()->route('erp.orders.index');
    })->name('filament.erp.resources.orders.index');

    // 4. Fallback redirect for legacy /admin routes to /erp
    Route::any('/admin/{any?}', function ($any = null) {
        return redirect('/erp' . ($any ? '/' . $any : ''));
    })->where('any', '.*');
});