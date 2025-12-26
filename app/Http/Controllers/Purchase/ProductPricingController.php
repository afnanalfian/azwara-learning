<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Course;

class ProductPricingController extends Controller
{
    /**
     * =========================
     * LIST PRICING RULES
     * =========================
     */
    public function index()
    {
        $rules = PricingRule::with('priceable')
            ->orderBy('product_type')
            ->orderBy('priceable_type')
            ->orderBy('min_qty')
            ->get();

        return view('purchase.pricing.index', compact('rules'));
    }

    /**
     * =========================
     * FORM CREATE
     * =========================
     */
    public function create()
    {
        $productTypes = [
            'meeting'        => 'Meeting',
            'tryout'         => 'Tryout',
            'course_package' => 'Course Package',
            'addon'          => 'Addon',
        ];

        return view('purchase.pricing.create', [
            'productTypes' => $productTypes,
            'courses'      => Course::orderBy('id')->get(),
            'tryouts'      => Exam::where('type', 'tryout')->orderBy('title')->get(),
        ]);
    }

    /**
     * =========================
     * STORE PRICING RULE
     * =========================
     */
    public function store(Request $request)
    {

        /**
         * 1. VALIDASI
         */
        $data = $request->validate([
            'product_type'   => 'required|in:meeting,tryout,course_package,addon',
            'pricing_type'   => 'required|in:per_unit,fixed',
            'min_qty'        => 'required|integer|min:1',
            'max_qty'        => 'nullable|integer|gte:min_qty',
            'price'          => 'required|numeric|min:0',
            'is_active'      => 'sometimes|boolean',
            'price_scope'    => 'required|in:global,specific',
            'priceable_type' => 'nullable|string',
            'priceable_id'   => 'nullable|integer',
        ]);

        /**
         * 2. HANDLE SCOPE
         */
        if ($data['price_scope'] === 'global') {
            $data['priceable_type'] = null;
            $data['priceable_id'] = null;
        } else {
            // Untuk specific, pastikan priceable diisi
            if (empty($data['priceable_type']) || empty($data['priceable_id'])) {
                toast('priceable_id','Target spesifik harus dipilih');
                return back();
            }

            // Validasi tipe priceable sesuai product_type
            if ($data['product_type'] === 'meeting' || $data['product_type'] === 'course_package') {
                if ($data['priceable_type'] !== Course::class) {
                    toast('priceable_type', 'Tipe priceable tidak sesuai dengan product type');
                    return back();
                }
            }
            elseif ($data['product_type'] === 'tryout') {
                if ($data['priceable_type'] !== Exam::class) {
                    toast('error', 'Tipe priceable tidak sesuai dengan product type');
                    return back();
                }
            }

            // Addon tidak boleh specific
            if ($data['product_type'] === 'addon') {
                toast('product_type','Addon hanya bisa memiliki harga global');
                return back();
            }
        }

        /**
         * 3. CEK OVERLAP QTY
         */
        $min = $data['min_qty'];
        $max = $data['max_qty'] ?? PHP_INT_MAX;

        $overlap = PricingRule::where('product_type', $data['product_type'])
            ->where('is_active', true)
            ->where('priceable_type', $data['priceable_type'])
            ->where('priceable_id', $data['priceable_id'])
            ->where(function ($q) use ($min, $max) {
                $q->where('min_qty', '<=', $max)
                  ->where(function ($q) use ($min) {
                      $q->whereNull('max_qty')
                        ->orWhere('max_qty', '>=', $min);
                  });
            })
            ->exists();

        if ($overlap) {
            toast('min_qty', 'Range quantity bertabrakan dengan pricing rule yang sudah ada');
            return back();
        }

        /**
         * 4. NORMALISASI HARGA
         */
        if ($data['pricing_type'] === 'per_unit') {
            $data['price_per_unit'] = $data['price'];
            $data['fixed_price'] = null;
        } else {
            $data['fixed_price'] = $data['price'];
            $data['price_per_unit'] = null;
        }

        unset($data['pricing_type'], $data['price']);

        /**
         * 5. DEFAULT VALUES
         */
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        /**
         * 6. SIMPAN
         */
        PricingRule::create($data);

        toast('success', 'Pricing rule berhasil ditambahkan!');
        return redirect()->route('pricing.index');
    }


    /**
     * =========================
     * FORM EDIT
     * =========================
     */
    public function edit(PricingRule $pricingRule)
    {
        $productTypes = [
            'meeting'        => 'Meeting',
            'tryout'         => 'Tryout',
            'course_package' => 'Course Package',
            'addon'          => 'Addon',
        ];

        return view('purchase.pricing.edit', [
            'pricingRule' => $pricingRule,
            'productTypes'=> $productTypes,
        ]);
    }

    /**
     * =========================
     * UPDATE PRICING RULE
     * =========================
     */
    public function update(Request $request, PricingRule $pricingRule)
    {
        /**
         * 1. VALIDASI
         */
        $data = $request->validate([
            'pricing_type' => 'required|in:per_unit,fixed',
            'min_qty'      => 'required|integer|min:1',
            'max_qty'      => 'nullable|integer|gte:min_qty',
            'price'        => 'required|numeric|min:0',
            'is_active'    => 'sometimes|boolean',
        ]);

        /**
         * 2. VALIDASI OVERLAP (EXCLUDE SELF, SAME SCOPE)
         */
        $min = $data['min_qty'];
        $max = $data['max_qty'] ?? PHP_INT_MAX;

        $overlap = PricingRule::where('product_type', $pricingRule->product_type)
            ->where('id', '!=', $pricingRule->id)
            ->where('is_active', true)
            ->where('priceable_type', $pricingRule->priceable_type)
            ->where('priceable_id', $pricingRule->priceable_id)
            ->where(function ($q) use ($min, $max) {
                $q->where('min_qty', '<=', $max)
                ->where(function ($q) use ($min) {
                    $q->whereNull('max_qty')
                        ->orWhere('max_qty', '>=', $min);
                });
            })
            ->exists();

        if ($overlap) {
            toast('error','Range qty bertabrakan dengan pricing rule lain');
            return back();
        }

        /**
         * 3. NORMALISASI HARGA
         */
        $payload = [
            'min_qty'        => $data['min_qty'],
            'max_qty'        => $data['max_qty'],
            'price_per_unit' => null,
            'fixed_price'    => null,
        ];

        if ($data['pricing_type'] === 'per_unit') {
            $payload['price_per_unit'] = $data['price'];
        } else {
            $payload['fixed_price'] = $data['price'];
        }

        /**
         * 4. UPDATE
         */
        $pricingRule->update([
            ...$payload,
            'is_active' => $data['is_active'] ?? $pricingRule->is_active,
        ]);

        toast('success', 'Pricing rule berhasil diperbarui');
        return redirect()->route('pricing.index');
    }

    /**
     * =========================
     * DELETE
     * =========================
     */
    public function destroy(PricingRule $pricingRule)
    {
        $pricingRule->delete();

        toast('success', 'Pricing rule berhasil dihapus');
        return redirect()->route('pricing.index');
    }

    /**
     * =========================
     * TOGGLE ACTIVE
     * =========================
     */
    public function toggle(PricingRule $pricingRule)
    {
        $pricingRule->update([
            'is_active' => ! $pricingRule->is_active,
        ]);

        toast('success', 'Status pricing rule diperbarui');
        return redirect()->route('pricing.index');
    }
}
