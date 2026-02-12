<?php

use Illuminate\Support\Facades\Gate;
use PictaStudio\VenditioCore\VenditioCore;

it('registers the policy correctly', function () {
    if (!config('venditio-core.policies.register')) {
        return;
    }

    VenditioCore::registerPolicies();

    foreach (config('venditio-core.models') as $contract => $model) {
        $model = class_basename($model);

        if (!class_exists("PictaStudio\VenditioCore\Policies\\{$model}Policy")) {
            continue;
        }

        expect(Gate::getPolicyFor("PictaStudio\VenditioCore\Models\\{$model}")::class)
            ->toBe("PictaStudio\VenditioCore\Policies\\{$model}Policy");
    }
})->group('policy');
