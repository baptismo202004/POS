<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncBranchStocksFromStockIns extends Command
{
    protected $signature = 'inventory:sync-branch-stocks-from-stock-ins {--dry-run : Only show what would change}';

    protected $description = 'Ensures branch_stocks.quantity_base is at least the sum of stock_ins.quantity per branch/product (useful for backfilling older stock-ins).';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $rows = DB::table('stock_ins')
            ->select([
                'branch_id',
                'product_id',
                DB::raw('COALESCE(SUM(quantity), 0) as qty_base_sum'),
            ])
            ->groupBy('branch_id', 'product_id')
            ->get();

        $updated = 0;
        $created = 0;
        $skipped = 0;

        foreach ($rows as $r) {
            $branchId = (int) ($r->branch_id ?? 0);
            $productId = (int) ($r->product_id ?? 0);
            $sum = (float) ($r->qty_base_sum ?? 0);

            if ($branchId <= 0 || $productId <= 0 || $sum <= 0) {
                $skipped++;
                continue;
            }

            $existing = DB::table('branch_stocks')
                ->where('branch_id', $branchId)
                ->where('product_id', $productId)
                ->first();

            if (!$existing) {
                if (!$dryRun) {
                    DB::table('branch_stocks')->insert([
                        'branch_id' => $branchId,
                        'product_id' => $productId,
                        'quantity_base' => $sum,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $created++;
                continue;
            }

            $current = (float) ($existing->quantity_base ?? 0);
            if ($current >= $sum) {
                $skipped++;
                continue;
            }

            if (!$dryRun) {
                DB::table('branch_stocks')
                    ->where('branch_id', $branchId)
                    ->where('product_id', $productId)
                    ->update([
                        'quantity_base' => $sum,
                        'updated_at' => now(),
                    ]);
            }

            $updated++;
        }

        $this->info('Sync complete.');
        $this->line('Created: ' . $created);
        $this->line('Updated: ' . $updated);
        $this->line('Skipped: ' . $skipped);

        if ($dryRun) {
            $this->warn('Dry run mode: no changes were written.');
        }

        return self::SUCCESS;
    }
}
