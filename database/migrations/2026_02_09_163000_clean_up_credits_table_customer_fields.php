<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, migrate existing customer data to customers table if needed
        $this->migrateExistingCreditCustomers();

        // Then add customer_id column back to credits table
        Schema::table('credits', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->onDelete('set null');
        });

        // Now link existing credits to customers based on customer_name
        $this->linkCreditsToCustomers();

        // Finally, remove redundant customer fields from credits table
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'phone', 'email', 'address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->string('phone')->nullable()->after('customer_name');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');
        });

        // Restore data from customers table (optional - complex operation)
        $this->restoreCustomerFieldsToCredits();

        Schema::table('credits', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }

    /**
     * Migrate existing customer data from credits to customers table
     */
    private function migrateExistingCreditCustomers()
    {
        // Get all unique customer names from credits table
        $creditCustomers = DB::table('credits')
            ->select('customer_name', 'phone', 'email', 'address')
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->distinct()
            ->get();

        foreach ($creditCustomers as $creditCustomer) {
            // Check if customer already exists by phone or email
            $existingCustomer = null;
            
            if (!empty($creditCustomer->phone)) {
                $existingCustomer = DB::table('customers')
                    ->where('phone', $creditCustomer->phone)
                    ->first();
            }
            
            if (!$existingCustomer && !empty($creditCustomer->email)) {
                $existingCustomer = DB::table('customers')
                    ->where('email', $creditCustomer->email)
                    ->first();
            }

            if (!$existingCustomer) {
                // Create new customer
                DB::table('customers')->insert([
                    'full_name' => $creditCustomer->customer_name,
                    'phone' => $creditCustomer->phone ?? null,
                    'email' => $creditCustomer->email ?? null,
                    'address' => $creditCustomer->address ?? null,
                    'max_credit_limit' => 0,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Link existing credits to customers based on customer_name
     */
    private function linkCreditsToCustomers()
    {
        // Get all credits and link them to customers
        $credits = DB::table('credits')->get();

        foreach ($credits as $credit) {
            if (!empty($credit->customer_name)) {
                // Find customer by name, phone, or email
                $customer = null;
                
                if (!empty($credit->phone)) {
                    $customer = DB::table('customers')
                        ->where('phone', $credit->phone)
                        ->first();
                }
                
                if (!$customer && !empty($credit->email)) {
                    $customer = DB::table('customers')
                        ->where('email', $credit->email)
                        ->first();
                }
                
                if (!$customer) {
                    $customer = DB::table('customers')
                        ->where('full_name', $credit->customer_name)
                        ->first();
                }

                if ($customer) {
                    // Update credit with customer_id
                    DB::table('credits')
                        ->where('id', $credit->id)
                        ->update(['customer_id' => $customer->id]);
                }
            }
        }
    }

    /**
     * Restore customer fields to credits table (for rollback)
     */
    private function restoreCustomerFieldsToCredits()
    {
        // Get all credits with customer_id and restore customer fields
        $credits = DB::table('credits')
            ->join('customers', 'credits.customer_id', '=', 'customers.id')
            ->select('credits.id', 'customers.full_name', 'customers.phone', 'customers.email', 'customers.address')
            ->get();

        foreach ($credits as $credit) {
            DB::table('credits')
                ->where('id', $credit->id)
                ->update([
                    'customer_name' => $credit->full_name,
                    'phone' => $credit->phone,
                    'email' => $credit->email,
                    'address' => $credit->address,
                ]);
        }
    }
};
