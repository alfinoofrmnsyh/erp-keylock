<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeRupiahInputs
{
    /**
     * Financial field keys that should be normalized from Indonesian Rupiah formatted
     * strings (e.g. "1.500.000", "Rp 1.500.000", "1.000") to pure numeric integer/float values.
     *
     * Note: Non-financial fields such as quantity, rental_duration, duration, hour_meter,
     * year, tax_percent, pph_percent, phone, etc. are strictly excluded to preserve their values.
     */
    protected array $financialKeys = [
        'unit_price',
        'unit_cost',
        'cost_price',
        'cost',
        'logistics_cost',
        'vendor_cost',
        'shipping_fee',
        'shipping_delivery_fee',
        'shipping_return_fee',
        'subtotal',
        'tax_amount',
        'pph_amount',
        'discount_amount',
        'discount',
        'total_amount',
        'deposit_amount',
        'amount',
        'pay_amount',
        'paid_amount',
        'balance_due',
        'sale_price',
        'rental_rate_monthly',
        'rental_rate_daily',
        'overtime_rate_per_hour',
        'estimated_budget',
        'estimated_value',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        if (!empty($input)) {
            $normalized = $this->normalizeArray($input);
            $request->replace($normalized);
        }

        return $next($request);
    }

    /**
     * Recursively normalize inputs matching financial keys.
     */
    public function normalizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->normalizeArray($value);
            } elseif (is_string($value) && in_array((string)$key, $this->financialKeys, true)) {
                $data[$key] = $this->normalizeRupiahValue($value);
            }
        }

        return $data;
    }

    /**
     * Normalize a single Rupiah value to clean numeric integer/float value.
     */
    public function normalizeRupiahValue(string $value): mixed
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return $value;
        }

        // 1. Detect and preserve negative sign
        $isNegative = false;
        $work = $trimmed;
        if (str_starts_with($work, '-')) {
            $isNegative = true;
            $work = ltrim(substr($work, 1));
        }

        // 2. Strip currency prefix ("Rp", "rp", "IDR") if present
        if (preg_match('/^(?:Rp|IDR)\s*/i', $work, $matches)) {
            $work = substr($work, strlen($matches[0]));
            $work = trim($work);
        }

        // Re-check minus if placed after currency prefix, e.g. "Rp -1.000.000"
        if (!$isNegative && str_starts_with($work, '-')) {
            $isNegative = true;
            $work = ltrim(substr($work, 1));
        }

        // 3. Check if pure integer string (e.g. "1000000", "0")
        if (preg_match('/^\d+$/', $work)) {
            $val = (int) $work;
            return $isNegative ? -$val : $val;
        }

        // 4. Indonesian formatted Rupiah with dots as thousand separators:
        // Examples: "1.000", "250.000", "1.000.000", "15.000.000", "1.500,50", "1.500.000,00"
        if (preg_match('/^\d{1,3}(?:\.\d{3})+(?:,(\d{1,2}))?$/', $work, $m)) {
            $integerPart = str_replace('.', '', explode(',', $work)[0]);
            $decimalPart = $m[1] ?? null;

            if ($decimalPart !== null && (int)$decimalPart > 0) {
                $val = (float) ($integerPart . '.' . $decimalPart);
            } else {
                $val = (int) $integerPart;
            }

            return $isNegative ? -$val : $val;
        }

        // 5. Decimal comma without thousand dots (e.g. "1500,50" or "1500,00")
        if (preg_match('/^\d+,(\d{1,2})$/', $work, $m)) {
            $parts = explode(',', $work);
            $integerPart = $parts[0];
            $decimalPart = $m[1];

            if ((int)$decimalPart > 0) {
                $val = (float) ($integerPart . '.' . $decimalPart);
            } else {
                $val = (int) $integerPart;
            }

            return $isNegative ? -$val : $val;
        }

        // 6. Standard dot-decimal float with 1 or 2 decimal places (e.g. "1500.50", "12.5", "0.75")
        // Note: strictly 1-2 digits after dot so it never collides with dot thousand separators (.000)
        if (preg_match('/^\d+\.\d{1,2}$/', $work)) {
            $val = (float) $work;
            return $isNegative ? -$val : $val;
        }

        // 7. If input does not strictly match a valid Rupiah or numeric format,
        // leave it untouched so Laravel's native validation rules can catch and report it properly.
        return $value;
    }
}
