<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Productable;
use App\Models\Meeting;
use App\Models\Course;
use App\Models\Exam;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductAutoCreateObserver
{
    public function created($model): void
    {
        try {
            // CEK DENGAN CARA YANG AMAN
            $hasProduct = false;

            // Coba cek melalui productable
            if (method_exists($model, 'productable')) {
                $hasProduct = $model->productable()->exists();
            }

            // Fallback: cek langsung di tabel productables
            if (!$hasProduct) {
                $hasProduct = Productable::where('productable_type', get_class($model))
                    ->where('productable_id', $model->id)
                    ->exists();
            }

            if ($hasProduct) {
                return;
            }

            match (true) {
                $model instanceof Meeting =>
                    $this->createProduct(
                        'meeting',
                        $model->title,
                        Meeting::class,
                        $model->id
                    ),

                $model instanceof Course =>
                    $this->createProduct(
                        'course_package',
                        $model->name,
                        Course::class,
                        $model->id
                    ),

                $model instanceof Exam && $model->type === 'tryout' =>
                    $this->createProduct(
                        'tryout',
                        $model->title,
                        Exam::class,
                        $model->id
                    ),

                default => null,
            };

        } catch (\Exception $e) {
            Log::error('ProductAutoCreateObserver failed', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function createProduct(
        string $type,
        string $name,
        string $productableType,
        int $productableId
    ): void {
        try {
            DB::transaction(function () use ($type, $name, $productableType, $productableId) {
                $product = Product::create([
                    'type'        => $type,
                    'name'        => $name,
                    'is_active'   => true,
                ]);

                Productable::create([
                    'product_id'       => $product->id,
                    'productable_type' => $productableType,
                    'productable_id'   => $productableId,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to create product', [
                'type' => $type,
                'name' => $name,
                'productable_type' => $productableType,
                'productable_id' => $productableId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
