<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CompanyConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    /**
     * Financial & Invoicing Hub
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'invoices');
        if (!in_array($tab, ['invoices', 'bills', 'cashflow'])) {
            $tab = 'invoices';
        }

        $search = $request->query('search');
        $statusFilter = $request->query('status', 'all');

        $totalCustomerInvoicesCount = Invoice::customerInvoices()->count();
        $totalVendorBillsCount = Invoice::vendorBills()->count();

        // 1. Operational KPI Cards for Tagihan Pelanggan (AR)
        // a. Transaksi aktif yang belum tergenerate invoice
        $unbilledOrdersQuery = Order::whereIn('order_type', ['sales_order', 'rental_order'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) {
                $q->whereNull('invoice_status')
                  ->orWhere('invoice_status', 'unbilled');
            });
        $unbilledOrdersCount = $unbilledOrdersQuery->count();
        $unbilledOrdersAmount = $unbilledOrdersQuery->sum('total_amount');

        // b. Invoice overdue (lewat jatuh tempo)
        $overdueInvoicesQuery = Invoice::customerInvoices()
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now()->format('Y-m-d'));
        $overdueInvoicesCount = $overdueInvoicesQuery->count();
        $overdueInvoicesAmount = $overdueInvoicesQuery->sum('balance_due');

        // c. Invoice paid (lunas)
        $paidInvoicesQuery = Invoice::customerInvoices()->where('status', 'paid');
        $paidInvoicesCount = $paidInvoicesQuery->count();
        $paidInvoicesAmount = $paidInvoicesQuery->sum('total_amount');

        // d. Invoice belum masuk pembayaran sama sekali / belum DP
        $unpaidInvoicesQuery = Invoice::customerInvoices()
            ->where('status', '!=', 'paid')
            ->where('paid_amount', 0);
        $unpaidInvoicesCount = $unpaidInvoicesQuery->count();
        $unpaidInvoicesAmount = $unpaidInvoicesQuery->sum('balance_due');

        // 2. Operational KPI Cards for Tagihan Vendor (AP)
        // a. PO Vendor belum terbit tagihan / invoice
        $unbilledPosQuery = Order::purchaseOrders()
            ->where(function ($q) {
                $q->whereNull('invoice_status')
                  ->orWhere('invoice_status', 'unbilled');
            });
        $unbilledPosCount = $unbilledPosQuery->count();
        $unbilledPosAmount = $unbilledPosQuery->sum('total_amount');

        // b. Tagihan vendor belum ada pembayaran / belum DP
        $unpaidBillsQuery = Invoice::vendorBills()
            ->where('status', '!=', 'paid')
            ->where('paid_amount', 0);
        $unpaidBillsCount = $unpaidBillsQuery->count();
        $unpaidBillsAmount = $unpaidBillsQuery->sum('balance_due');

        // c. Tagihan vendor overdue (lewat tempo)
        $overdueBillsQuery = Invoice::vendorBills()
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now()->format('Y-m-d'));
        $overdueBillsCount = $overdueBillsQuery->count();
        $overdueBillsAmount = $overdueBillsQuery->sum('balance_due');

        // d. Tagihan vendor lunas
        $paidBillsQuery = Invoice::vendorBills()->where('status', 'paid');
        $paidBillsCount = $paidBillsQuery->count();
        $paidBillsAmount = $paidBillsQuery->sum('total_amount');

        $perPage = (int) $request->query('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        // 3. Data for Tab: Invoices (Customer Receivables / AR)
        $invoicesQuery = Invoice::customerInvoices()
            ->with(['contact', 'order', 'orders', 'payments']);

        if ($statusFilter !== 'all' && !empty($statusFilter) && $tab === 'invoices') {
            if ($statusFilter === 'overdue') {
                $invoicesQuery->where('status', '!=', 'paid')->where('due_date', '<', now()->format('Y-m-d'));
            } else {
                $invoicesQuery->where('status', $statusFilter);
            }
        }

        if (!empty($search) && $tab === 'invoices') {
            $invoicesQuery->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('contact', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }
        $customerInvoices = $invoicesQuery->latest('issue_date')->latest('id')->paginate($perPage)->withQueryString();

        // 4. Data for Tab: Bills (Vendor Payables / AP)
        $billsQuery = Invoice::vendorBills()
            ->with(['contact', 'order', 'customerOrder.contact', 'payments']);

        if ($statusFilter !== 'all' && !empty($statusFilter) && $tab === 'bills') {
            if ($statusFilter === 'overdue') {
                $billsQuery->where('status', '!=', 'paid')->where('due_date', '<', now()->format('Y-m-d'));
            } else {
                $billsQuery->where('status', $statusFilter);
            }
        }

        if (!empty($search) && $tab === 'bills') {
            $billsQuery->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('vendor_ref_no', 'like', "%{$search}%")
                  ->orWhereHas('contact', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        $vendorBills = $billsQuery->latest('issue_date')->latest('id')->paginate($perPage)->withQueryString();

        // 5. Data for Tab: Cashflow (Payments In & Out)
        $paymentsQuery = Payment::with(['invoice.contact', 'invoice.order', 'creator']);

        if ($statusFilter !== 'all' && !empty($statusFilter) && $tab === 'cashflow') {
            if (in_array($statusFilter, ['in', 'out'])) {
                $paymentsQuery->where('payment_type', $statusFilter);
            }
        }

        if (!empty($search) && $tab === 'cashflow') {
            $paymentsQuery->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('bank_account', 'like', "%{$search}%")
                  ->orWhereHas('invoice.contact', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        $payments = $paymentsQuery->latest('payment_date')->latest('id')->paginate($perPage)->withQueryString();

        // Unbilled Orders & POs for quick-generation modal
        $unbilledOrders = Order::whereIn('order_type', ['sales_order', 'rental_order'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) {
                $q->whereNull('invoice_status')
                  ->orWhere('invoice_status', 'unbilled');
            })
            ->with(['contact', 'items.item'])
            ->latest('order_date')
            ->latest('id')
            ->get();

        $unbilledPos = Order::purchaseOrders()
            ->where('invoice_status', 'unbilled')
            ->with(['contact'])
            ->latest('id')
            ->get();

        $customers = Contact::where('type', 'customer')->orderBy('name')->get();
        $vendors = Contact::where('type', 'vendor')->orderBy('name')->get();

        return view('erp.finance', compact(
            'tab',
            'search',
            'statusFilter',
            'totalCustomerInvoicesCount',
            'totalVendorBillsCount',
            'unbilledOrdersCount',
            'unbilledOrdersAmount',
            'overdueInvoicesCount',
            'overdueInvoicesAmount',
            'paidInvoicesCount',
            'paidInvoicesAmount',
            'unpaidInvoicesCount',
            'unpaidInvoicesAmount',
            'unbilledPosCount',
            'unbilledPosAmount',
            'unpaidBillsCount',
            'unpaidBillsAmount',
            'overdueBillsCount',
            'overdueBillsAmount',
            'paidBillsCount',
            'paidBillsAmount',
            'customerInvoices',
            'vendorBills',
            'payments',
            'unbilledOrders',
            'unbilledPos',
            'customers',
            'vendors'
        ));
    }

    /**
     * Store Customer Invoice
     */
    public function storeInvoice(Request $request)
    {
        abort_if(!auth()->user()?->isFinance(), 403, 'Akses Ditolak: Hanya staf Keuangan atau Administrator yang dapat memproses faktur.');

        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'order_ids' => 'nullable|array',
            'order_ids.*' => 'exists:orders,id',
            'contact_id' => 'required|exists:contacts,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_terms' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $nextInv = Invoice::customerInvoices()->count() + 1;
            $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad($nextInv, 3, '0', STR_PAD_LEFT);

            // Determine all selected order IDs
            $selectedOrderIds = [];
            if (!empty($validated['order_ids']) && is_array($validated['order_ids'])) {
                $selectedOrderIds = array_filter($validated['order_ids']);
            }
            if (!empty($validated['order_id']) && !in_array($validated['order_id'], $selectedOrderIds)) {
                $selectedOrderIds[] = $validated['order_id'];
            }
            $primaryOrderId = !empty($selectedOrderIds) ? $selectedOrderIds[0] : null;

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'invoice_type' => 'customer_invoice',
                'order_id' => $primaryOrderId,
                'contact_id' => $validated['contact_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $validated['subtotal'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'paid_amount' => 0.00,
                'balance_due' => $validated['total_amount'],
                'status' => 'sent',
                'payment_terms' => $validated['payment_terms'] ?? 'Net 30 Days',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id() ?? 1,
            ]);

            if (!empty($selectedOrderIds)) {
                Order::whereIn('id', $selectedOrderIds)->update([
                    'invoice_id' => $invoice->id,
                    'invoice_status' => 'invoiced',
                ]);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Invoice Penjualan {$invoiceNumber} berhasil diterbitkan!",
                    'invoice' => $invoice->load(['contact', 'order', 'orders']),
                ]);
            }

            return redirect()->route('erp.finance.index', ['tab' => 'invoices'])
                ->with('success', "Invoice Penjualan {$invoiceNumber} berhasil diterbitkan!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', 'Gagal membuat Invoice: ' . $e->getMessage());
        }
    }

    /**
     * Store Vendor Bill (Tagihan Vendor)
     */
    public function storeVendorBill(Request $request)
    {
        abort_if(!auth()->user()?->isFinance(), 403, 'Akses Ditolak: Hanya staf Keuangan atau Administrator yang dapat mencatat tagihan vendor.');

        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'customer_order_id' => 'nullable|exists:orders,id',
            'contact_id' => 'required|exists:contacts,id',
            'vendor_ref_no' => 'nullable|string|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $nextBill = Invoice::vendorBills()->count() + 1;
            $billNumber = 'BILL-' . date('Ym') . '-' . str_pad($nextBill, 3, '0', STR_PAD_LEFT);

            $bill = Invoice::create([
                'invoice_number' => $billNumber,
                'vendor_ref_no' => $validated['vendor_ref_no'] ?? null,
                'invoice_type' => 'vendor_bill',
                'order_id' => $validated['order_id'] ?? null,
                'customer_order_id' => $validated['customer_order_id'] ?? null,
                'contact_id' => $validated['contact_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $validated['subtotal'],
                'discount_amount' => 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'paid_amount' => 0.00,
                'balance_due' => $validated['total_amount'],
                'status' => 'sent',
                'payment_terms' => 'Net 30 Days',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id() ?? 1,
            ]);

            if (!empty($validated['order_id'])) {
                Order::where('id', $validated['order_id'])->update(['invoice_status' => 'invoiced']);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Tagihan Vendor {$billNumber} berhasil dicatat!",
                    'bill' => $bill->load(['contact', 'order']),
                ]);
            }

            return redirect()->route('erp.finance.index', ['tab' => 'bills'])
                ->with('success', "Tagihan Vendor {$billNumber} berhasil dicatat!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', 'Gagal mencatat Tagihan Vendor: ' . $e->getMessage());
        }
    }

    /**
     * Store Payment (Cash In or Cash Out)
     */
    public function storePayment(Request $request)
    {
        abort_if(!auth()->user()?->isFinance(), 403, 'Akses Ditolak: Hanya staf Keuangan atau Administrator yang dapat mencatat pembayaran.');

        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:bank_transfer,cash,giro,credit_card,qris',
            'bank_account' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::with('order')->findOrFail($validated['invoice_id']);
        $isCashIn = ($invoice->invoice_type === 'customer_invoice');
        $paymentType = $isCashIn ? 'in' : 'out';

        DB::beginTransaction();
        try {
            $prefix = $isCashIn ? 'PAY-IN' : 'PAY-OUT';
            $nextPay = Payment::where('payment_type', $paymentType)->count() + 1;
            $paymentNumber = "{$prefix}-" . date('Ym') . '-' . str_pad($nextPay, 3, '0', STR_PAD_LEFT);

            $proofPath = null;
            if ($request->hasFile('proof_image')) {
                $proofPath = $request->file('proof_image')->store('payments', 'public');
            }

            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'payment_type' => $paymentType,
                'invoice_id' => $invoice->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'bank_account' => $validated['bank_account'] ?? 'BCA - Keylock',
                'reference_number' => $validated['reference_number'] ?? null,
                'proof_image' => $proofPath,
                'status' => 'verified',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id() ?? 1,
            ]);

            // Automatically recalculate invoice status & balance due
            $invoice->recalculatePaymentStatus();

            // If customer invoice and fully paid, mark customer order(s) as paid
            if ($invoice->invoice_type === 'customer_invoice' && $invoice->status === 'paid') {
                Order::where('invoice_id', $invoice->id)->update(['invoice_status' => 'paid']);
                if ($invoice->order_id) {
                    Order::where('id', $invoice->order_id)->update(['invoice_status' => 'paid']);
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => ($isCashIn ? 'Pembayaran Pelanggan' : 'Pembayaran ke Vendor') . " ({$paymentNumber}) berhasil dicatat!",
                    'payment' => $payment->load('invoice'),
                    'invoice' => $invoice->fresh(),
                ]);
            }

            $targetTab = $isCashIn ? 'invoices' : 'bills';
            return redirect()->route('erp.finance.index', ['tab' => $targetTab])
                ->with('success', "Pembayaran {$paymentNumber} berhasil dicatat!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Official Invoice PDF / Print View
     */
    public function invoicePdf($id)
    {
        $invoice = Invoice::with(['contact', 'order.items.item', 'orders.items.item', 'customerOrder', 'payments', 'creator'])->findOrFail($id);

        $user = auth()->user();
        $canView = $user && (
            $user->isAdmin() ||
            $user->isFinance() ||
            (int)$invoice->created_by === (int)$user->id ||
            ($invoice->order && (int)$invoice->order->created_by === (int)$user->id)
        );
        abort_if(!$canView, 403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat faktur ini.');

        $company = [
            'name' => CompanyConfig::get('company_name', 'PT KEY LOCK INDONESIA'),
            'address' => CompanyConfig::get('company_address', 'Jalan Arteri KIIC Rolling Hills Ruko Pacific Plaza No. 91, Desa Margakaya, Kecamatan Telukjambe Barat, Kabupaten Karawang 41361.'),
            'npwp' => CompanyConfig::get('company_npwp', '1000 0000 0573 6973'),
            'phone' => CompanyConfig::get('company_phone', '0811 2345 6789'),
            'email' => CompanyConfig::get('company_email', 'keylockindo@outlook.com'),
            'director_name' => CompanyConfig::get('director_name', 'QI SHUJUN 齐书骏'),
            'director_title' => CompanyConfig::get('director_title', 'Presiden Direktur 董事长'),
            'bank_accounts' => json_decode(CompanyConfig::get('bank_accounts', '[]'), true),
            'terms' => CompanyConfig::get('invoice_terms', "1. Pembayaran ditransfer ke rekening resmi PT KEY LOCK INDONESIA.\n2. Cantumkan nomor invoice pada berita transfer."),
        ];

        return view('erp.invoice-pdf', compact('invoice', 'company'));
    }

    /**
     * Get Invoice / Bill Details JSON
     */
    public function getDetails($id)
    {
        $invoice = Invoice::with(['contact', 'order.items.item', 'orders.items.item', 'customerOrder.contact', 'payments'])->findOrFail($id);

        $user = auth()->user();
        $canView = $user && (
            $user->isAdmin() ||
            $user->isFinance() ||
            (int)$invoice->created_by === (int)$user->id ||
            ($invoice->order && (int)$invoice->order->created_by === (int)$user->id)
        );
        abort_if(!$canView, 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses data faktur ini.');

        return response()->json([
            'success' => true,
            'invoice' => $invoice,
        ]);
    }
}
