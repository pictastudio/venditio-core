<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('discounts', 'apply_to_cart_total')) {
                $table->boolean('apply_to_cart_total')
                    ->default(false)
                    ->after('max_uses');
            }

            if (!Schema::hasColumn('discounts', 'apply_once_per_cart')) {
                $table->boolean('apply_once_per_cart')
                    ->default(false)
                    ->after('apply_to_cart_total');
            }

            if (!Schema::hasColumn('discounts', 'max_uses_per_user')) {
                $table->unsignedInteger('max_uses_per_user')
                    ->nullable()
                    ->after('apply_once_per_cart');
            }

            if (!Schema::hasColumn('discounts', 'one_per_user')) {
                $table->boolean('one_per_user')
                    ->default(false)
                    ->after('max_uses_per_user');
            }

            if (!Schema::hasColumn('discounts', 'free_shipping')) {
                $table->boolean('free_shipping')
                    ->default(false)
                    ->after('one_per_user');
            }

            if (!Schema::hasColumn('discounts', 'minimum_order_total')) {
                $table->decimal('minimum_order_total', 10, 2)
                    ->nullable()
                    ->after('free_shipping');
            }
        });

        if (Schema::hasColumn('discounts', 'rules')) {
            DB::table('discounts')
                ->select(['id', 'discountable_type', 'discountable_id', 'rules'])
                ->orderBy('id')
                ->chunkById(250, function ($discounts): void {
                    foreach ($discounts as $discount) {
                        $rules = is_array($discount->rules)
                            ? $discount->rules
                            : json_decode((string) $discount->rules, true);

                        if (!is_array($rules) || $rules === []) {
                            continue;
                        }

                        $onlyForUserId = $rules['only_for_user_id']
                            ?? $rules['specific_user_id']
                            ?? $rules['user_id']
                            ?? null;

                        $minimumOrderTotal = $rules['minimum_order_total']
                            ?? $rules['minimum_total']
                            ?? $rules['min_total']
                            ?? null;

                        $maxUsesPerUser = $rules['max_uses_per_user'] ?? null;
                        $onePerUser = (bool) ($rules['one_per_user'] ?? false);

                        if (!$onePerUser && is_numeric($maxUsesPerUser) && (int) $maxUsesPerUser === 1) {
                            $onePerUser = true;
                        }

                        $updates = [
                            'apply_to_cart_total' => (bool) ($rules['apply_to_cart_total'] ?? false),
                            'apply_once_per_cart' => (bool) ($rules['apply_once_per_cart'] ?? false),
                            'max_uses_per_user' => is_numeric($maxUsesPerUser) ? max(1, (int) $maxUsesPerUser) : null,
                            'one_per_user' => $onePerUser,
                            'free_shipping' => (bool) ($rules['free_shipping'] ?? false),
                            'minimum_order_total' => is_numeric($minimumOrderTotal) ? max(0, (float) $minimumOrderTotal) : null,
                        ];

                        if (
                            is_numeric($onlyForUserId)
                            && blank($discount->discountable_type)
                            && blank($discount->discountable_id)
                        ) {
                            $updates['discountable_type'] = 'user';
                            $updates['discountable_id'] = (int) $onlyForUserId;
                        }

                        DB::table('discounts')
                            ->where('id', $discount->id)
                            ->update($updates);
                    }
                });

            Schema::table('discounts', function (Blueprint $table) {
                $table->dropColumn('rules');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('discounts', 'rules')) {
            Schema::table('discounts', function (Blueprint $table) {
                $table->json('rules')->nullable()->after('max_uses');
            });
        }

        DB::table('discounts')
            ->select([
                'id',
                'apply_to_cart_total',
                'apply_once_per_cart',
                'max_uses_per_user',
                'one_per_user',
                'discountable_type',
                'discountable_id',
                'free_shipping',
                'minimum_order_total',
            ])
            ->orderBy('id')
            ->chunkById(250, function ($discounts): void {
                foreach ($discounts as $discount) {
                    $rules = array_filter([
                        'apply_to_cart_total' => (bool) $discount->apply_to_cart_total ? true : null,
                        'apply_once_per_cart' => (bool) $discount->apply_once_per_cart ? true : null,
                        'max_uses_per_user' => $discount->max_uses_per_user !== null ? (int) $discount->max_uses_per_user : null,
                        'one_per_user' => (bool) $discount->one_per_user ? true : null,
                        'free_shipping' => (bool) $discount->free_shipping ? true : null,
                        'minimum_order_total' => $discount->minimum_order_total !== null ? (float) $discount->minimum_order_total : null,
                    ], fn (mixed $value): bool => $value !== null);

                    if ($discount->discountable_type === 'user' && filled($discount->discountable_id)) {
                        $rules['only_for_user_id'] = (int) $discount->discountable_id;
                    }

                    DB::table('discounts')
                        ->where('id', $discount->id)
                        ->update([
                            'rules' => $rules === [] ? null : json_encode($rules, JSON_THROW_ON_ERROR),
                        ]);
                }
            });

        Schema::table('discounts', function (Blueprint $table) {
            if (Schema::hasColumn('discounts', 'minimum_order_total')) {
                $table->dropColumn('minimum_order_total');
            }

            if (Schema::hasColumn('discounts', 'free_shipping')) {
                $table->dropColumn('free_shipping');
            }

            if (Schema::hasColumn('discounts', 'one_per_user')) {
                $table->dropColumn('one_per_user');
            }

            if (Schema::hasColumn('discounts', 'max_uses_per_user')) {
                $table->dropColumn('max_uses_per_user');
            }

            if (Schema::hasColumn('discounts', 'apply_once_per_cart')) {
                $table->dropColumn('apply_once_per_cart');
            }

            if (Schema::hasColumn('discounts', 'apply_to_cart_total')) {
                $table->dropColumn('apply_to_cart_total');
            }
        });
    }
};
