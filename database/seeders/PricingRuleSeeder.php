<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Product;
use App\Models\PricingRule;
use Illuminate\Database\Seeder;

class PricingRuleSeeder extends Seeder
{
    /**
     * Harga fixed untuk berbagai jenis produk
     */
    private array $fixedPrices = [
        'course_package' => [
            'Paket Kursus Matematika Lengkap' => 1500000, // 1.5jt
            'Paket Kursus IPA Lengkap' => 1750000,        // 1.75jt
            'FleetSAR PALU' => 1200000,                   // 1.2jt
        ],

        'tryout' => [
            'Tryout Matematika' => 15000,
            'Tryout IPA' => 20000,
            'Tryout Testing' => 5000,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama
        PricingRule::truncate();

        // Ambil data yang dibutuhkan
        $courses = Course::all();
        $meetings = Product::where('type', 'meeting')->get();
        $tryouts = Exam::where('type', 'tryout')->get();
        $coursePackages = Product::where('type', 'course_package')->get();

        echo "Seeding pricing rules...\n";

        /**
         * 1. PRICING RULES UNTUK MEETING (SPESIFIC) - PER UNIT
         */
        foreach ($meetings as $meeting) {
            // Harga per pertemuan berbeda-beda
            $prices = [
                'Persamaan Kuadrat' => 75000,
                'Logaritma' => 80000,
                'Akar Pangkat' => 70000,
                'Hukum Newton' => 85000,
                'Asas Black' => 90000,
                'Tumbuhan' => 65000,
                'p1 bosku' => 50000,
                'p2 bosku' => 55000,
                'p3 bosku' => 60000,
                'p4 bosku' => 45000,
                'p5 bosku' => 70000,
                'p6 bosqyu' => 65000,
            ];

            $basePrice = $prices[$meeting->name] ?? 60000; // Default 60k

            // Buat 2-3 pricing rules per meeting (DISKON BERDASARKAN QTY)
            $rulesCount = rand(2, 3);

            for ($i = 0; $i < $rulesCount; $i++) {
                $minQty = $i === 0 ? 1 : ($i * 5) + 1; // 1, 6, 11, dst
                $maxQty = $i === $rulesCount - 1 ? null : ($i + 1) * 5; // 5, 10, null

                // Diskon untuk quantity lebih banyak
                $discount = $i * 10; // 0%, 10%, 20%
                $pricePerUnit = $basePrice * (1 - ($discount / 100));

                PricingRule::create([
                    'product_type' => 'meeting',
                    'min_qty' => $minQty,
                    'max_qty' => $maxQty,
                    'price_per_unit' => $pricePerUnit,
                    'fixed_price' => null,
                    'is_active' => true,
                    'priceable_type' => 'App\Models\Product',
                    'priceable_id' => $meeting->id,
                ]);

                echo "Created pricing rule for meeting: {$meeting->name} ";
                echo "(Qty: {$minQty}-" . ($maxQty ?? '∞') . ") ";
                echo "Price: Rp " . number_format($pricePerUnit, 0, ',', '.') . "/unit\n";
            }
        }

        /**
         * 2. PRICING RULES UNTUK COURSE PACKAGE (SPESIFIC) - FIXED PRICE
         */
        foreach ($coursePackages as $package) {
            // Gunakan harga dari array atau default
            $fixedPrice = $this->fixedPrices['course_package'][$package->name] ?? 1000000;

            // Course package hanya punya 1 pricing rule (fixed price)
            PricingRule::create([
                'product_type' => 'course_package',
                'min_qty' => 1,
                'max_qty' => null,
                'price_per_unit' => null,
                'fixed_price' => $fixedPrice,
                'is_active' => $package->is_active,
                'priceable_type' => 'App\Models\Product',
                'priceable_id' => $package->id,
            ]);

            echo "Created FIXED pricing rule for course package: {$package->name} ";
            echo "Price: Rp " . number_format($fixedPrice, 0, ',', '.') . "\n";
        }

        /**
         * 3. PRICING RULES UNTUK TRYOUT (SPESIFIC) - FIXED PRICE
         */
        foreach ($tryouts as $tryout) {
            // Gunakan harga dari array atau default
            $fixedPrice = $this->fixedPrices['tryout'][$tryout->title] ?? 10000;

            // Tryout hanya punya 1 pricing rule (fixed price)
            PricingRule::create([
                'product_type' => 'tryout',
                'min_qty' => 1,
                'max_qty' => null,
                'price_per_unit' => null,
                'fixed_price' => $fixedPrice,
                'is_active' => true,
                'priceable_type' => 'App\Models\Exam',
                'priceable_id' => $tryout->id,
            ]);

            echo "Created FIXED pricing rule for tryout: {$tryout->title} ";
            echo "Price: Rp " . number_format($fixedPrice, 0, ',', '.') . "\n";
        }

        /**
         * 4. PRICING RULES UNTUK ADDON (GLOBAL) - PER UNIT
         */
        // Buat 2 pricing rules global untuk addon dengan diskon quantity
        $addonBasePrice = 25000; // Rp 25k per addon

        // Rule 1: 1-10 addon
        PricingRule::create([
            'product_type' => 'addon',
            'min_qty' => 1,
            'max_qty' => 10,
            'price_per_unit' => null,
            'fixed_price' => $addonBasePrice,
            'is_active' => true,
            'priceable_type' => null,
            'priceable_id' => null,
        ]);
        echo "Created GLOBAL pricing rule for addon (Qty: 1-10) ";
        echo "Price: Rp " . number_format($addonBasePrice, 0, ',', '.') . "/unit\n";
        echo "Created GLOBAL FALLBACK pricing rule for course package\n";

        echo "\nPricing rules seeding completed!\n";
        echo "Total rules created: " . PricingRule::count() . "\n";
        echo "\nSummary:\n";
        echo "- Meeting: " . PricingRule::where('product_type', 'meeting')->count() . " rules (Per Unit)\n";
        echo "- Course Package: " . PricingRule::where('product_type', 'course_package')->count() . " rules (Fixed Price)\n";
        echo "- Tryout: " . PricingRule::where('product_type', 'tryout')->count() . " rules (Fixed Price)\n";
        echo "- Addon: " . PricingRule::where('product_type', 'addon')->count() . " rules (Per Unit, Global)\n";
    }
}
