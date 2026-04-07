<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_serial_id',
        'purchase_id',
        'purchase_item_id',
        'sale_id',
        'sale_item_id',
        'customer_id',
        'branch_id',
        'warranty_type',
        'coverage_months',
        'start_date',
        'expiry_date',
        'status',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSerial(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductSerial::class);
    }

    public function purchase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchaseItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function sale(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /** Recompute and save status based on today's date. */
    public function syncStatus(): void
    {
        if (in_array($this->status, ['voided', 'claimed'])) {
            return;
        }

        $this->status = ($this->expiry_date && $this->expiry_date->isPast())
            ? 'expired'
            : 'active';

        $this->save();
    }

    /** Days remaining (negative = already expired). */
    public function daysRemaining(): int
    {
        if (! $this->expiry_date) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->expiry_date, false);
    }

    /** Whether the warranty is currently active. */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->expiry_date
            && ! $this->expiry_date->isPast();
    }

    // ── Factory method ─────────────────────────────────────────────────────

    /**
     * Create a warranty record from a product's warranty settings.
     *
     * @param  array{
     *   product_id: int,
     *   warranty_type: string,
     *   coverage_months: int,
     *   start_date: string,
     *   quantity?: float,
     *   product_serial_id?: int|null,
     *   purchase_id?: int|null,
     *   purchase_item_id?: int|null,
     *   sale_id?: int|null,
     *   sale_item_id?: int|null,
     *   customer_id?: int|null,
     *   branch_id?: int|null,
     *   notes?: string|null,
     * } $data
     */
    public static function createFromProduct(array $data): self
    {
        $start = Carbon::parse($data['start_date']);
        $months = (int) ($data['coverage_months'] ?? 0);
        $expiry = $months > 0 ? $start->copy()->addMonths($months) : null;

        return self::create([
            'product_id' => $data['product_id'],
            'product_serial_id' => $data['product_serial_id'] ?? null,
            'purchase_id' => $data['purchase_id'] ?? null,
            'purchase_item_id' => $data['purchase_item_id'] ?? null,
            'sale_id' => $data['sale_id'] ?? null,
            'sale_item_id' => $data['sale_item_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'warranty_type' => $data['warranty_type'],
            'coverage_months' => $months,
            'start_date' => $start->toDateString(),
            'expiry_date' => $expiry?->toDateString(),
            'status' => 'active',
            'quantity' => $data['quantity'] ?? 1,
            'notes' => $data['notes'] ?? null,
        ]);
    }
}
