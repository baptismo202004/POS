<?php

namespace Database\Seeders\Pos;

use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\Sale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditSeeder extends Seeder
{
    public function run(): void
    {
        $cashierId = DB::table('users')->orderBy('id')->value('id');
        if (!$cashierId) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($cashierId, $now) {
            $creditSales = Sale::query()
                ->where('payment_method', 'credit')
                ->where('status', 'completed')
                ->orderBy('id')
                ->limit(500)
                ->get();

            foreach ($creditSales as $sale) {
                $at = $sale->created_at ?? $now;

                $creditAmount = (float) $sale->total_amount;
                if ($creditAmount <= 0) {
                    continue;
                }

                $reference = 'CR-' . $at->format('Y') . '-' . str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT);

                $credit = Credit::create([
                    'reference_number' => $reference,
                    'customer_id' => $sale->customer_id,
                    'sale_id' => $sale->id,
                    'cashier_id' => $sale->cashier_id ?? $cashierId,
                    'branch_id' => $sale->branch_id,
                    'credit_amount' => $creditAmount,
                    'paid_amount' => 0,
                    'remaining_balance' => $creditAmount,
                    'status' => 'active',
                    'date' => $at->toDateString(),
                    'notes' => 'Seeder credit transaction',
                    'credit_type' => 'cash',
                    'created_at' => $at,
                    'updated_at' => $at,
                ]);

                $paymentCount = random_int(1, 4);
                $paid = 0.0;

                for ($p = 1; $p <= $paymentCount; $p++) {
                    if ($paid >= $creditAmount) {
                        break;
                    }

                    $remaining = $creditAmount - $paid;
                    $pay = $p === $paymentCount
                        ? $remaining
                        : round($remaining * (random_int(10, 40) / 100), 2);

                    $pay = max(0.01, min($pay, $remaining));

                    $payAt = $at->copy()->addDays(random_int(1, 45));

                    CreditPayment::create([
                        'credit_id' => $credit->id,
                        'cashier_id' => $credit->cashier_id,
                        'payment_amount' => $pay,
                        'payment_method' => collect(['cash', 'card', 'bank_transfer'])->random(),
                        'notes' => 'Seeder payment',
                        'created_at' => $payAt,
                        'updated_at' => $payAt,
                    ]);

                    $paid += $pay;
                }

                $paid = round($paid, 2);
                $remainingBal = round($creditAmount - $paid, 2);

                $status = $remainingBal <= 0 ? 'paid' : 'active';

                $credit->update([
                    'paid_amount' => $paid,
                    'remaining_balance' => max(0, $remainingBal),
                    'status' => $status,
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
