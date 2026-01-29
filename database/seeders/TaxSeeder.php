<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tax;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            [
                'name' => 'Value Added Tax',
                'code' => 'VAT',
                'rate' => 12.00,
                'type' => 'percentage',
                'is_active' => true,
                'description' => 'Standard 12% Value Added Tax applicable to most goods and services in the Philippines.',
            ],
            [
                'name' => 'Expanded Value Added Tax',
                'code' => 'EVAT',
                'rate' => 12.00,
                'type' => 'percentage',
                'is_active' => true,
                'description' => 'Expanded VAT covering additional goods and services.',
            ],
            [
                'name' => 'Withholding Tax - Compensation',
                'code' => 'WHT-COMP',
                'rate' => 5.00,
                'type' => 'percentage',
                'is_active' => true,
                'description' => '5% withholding tax on compensation for certain suppliers.',
            ],
            [
                'name' => 'Withholding Tax - Professional Fees',
                'code' => 'WHT-PROF',
                'rate' => 10.00,
                'type' => 'percentage',
                'is_active' => true,
                'description' => '10% withholding tax on professional fees and services.',
            ],
            [
                'name' => 'Withholding Tax - Rentals',
                'code' => 'WHT-RENT',
                'rate' => 5.00,
                'type' => 'percentage',
                'is_active' => true,
                'description' => '5% withholding tax on rental payments.',
            ],
            [
                'name' => 'Documentary Stamp Tax',
                'code' => 'DST',
                'rate' => 1.50,
                'type' => 'percentage',
                'is_active' => true,
                'description' => 'Documentary Stamp Tax on certain documents and transactions.',
            ],
            [
                'name' => 'Local Business Tax',
                'code' => 'LBT',
                'rate' => 2.00,
                'type' => 'percentage',
                'is_active' => true,
                'description' => 'Local Business Tax imposed by local government units.',
            ],
            [
                'name' => 'Environmental Fee',
                'code' => 'ENV-FEE',
                'rate' => 0.50,
                'type' => 'percentage',
                'is_active' => true,
                'description' => 'Environmental fee for eco-friendly initiatives.',
            ],
            [
                'name' => 'Service Charge',
                'code' => 'SERVICE',
                'rate' => 10.00,
                'type' => 'percentage',
                'is_active' => true,
                'description' => '10% service charge applicable to restaurant and hospitality services.',
            ],
            [
                'name' => 'Fixed Processing Fee',
                'code' => 'PROC-FEE',
                'rate' => 50.00,
                'type' => 'fixed',
                'is_active' => true,
                'description' => 'Fixed processing fee for certain transactions.',
            ],
            [
                'name' => 'Zero-Rated Export',
                'code' => 'ZERO-EXP',
                'rate' => 0.00,
                'type' => 'percentage',
                'is_active' => true,
                'description' => 'Zero-rated tax for export transactions.',
            ],
            [
                'name' => 'Exempt Sales',
                'code' => 'EXEMPT',
                'rate' => 0.00,
                'type' => 'percentage',
                'is_active' => true,
                'description' => 'Tax-exempt sales as per BIR regulations.',
            ],
        ];

        foreach ($taxes as $tax) {
            Tax::create($tax);
        }
    }
}
