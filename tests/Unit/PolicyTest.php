<?php

use Illuminate\Support\Facades\Gate;
use PictaStudio\Venditio\Venditio;

it('registers the policy correctly', function () {
    if (!config('venditio.policies.register')) {
        return;
    }

    Venditio::registerPolicies();

    foreach (config('venditio.models') as $contract => $model) {
        $model = class_basename($model);

        if (!class_exists("PictaStudio\Venditio\Policies\\{$model}Policy")) {
            continue;
        }

        expect(Gate::getPolicyFor("PictaStudio\Venditio\Models\\{$model}")::class)
            ->toBe("PictaStudio\Venditio\Policies\\{$model}Policy");
    }
})->group('policy');
