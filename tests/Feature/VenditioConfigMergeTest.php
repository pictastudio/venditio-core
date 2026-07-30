<?php

namespace PictaStudio\Venditio\Tests\Feature;

use Orchestra\Testbench\Attributes\WithConfig;
use PictaStudio\Venditio\Pipelines\Cart\Pipes\CalculateTotals;
use PictaStudio\Venditio\Tests\TestCase;

final class VenditioConfigMergeTest extends TestCase
{
    #[WithConfig('venditio', [
        'routes' => [
            'api' => [
                'v1' => [
                    'prefix' => 'custom/venditio',
                ],
            ],
        ],
    ])]
    public function test_published_host_config_inherits_missing_nested_defaults(): void
    {
        $this->assertSame('custom/venditio', config('venditio.routes.api.v1.prefix'));
        $this->assertSame('api.venditio.v1', config('venditio.routes.api.v1.name'));
        $this->assertSame(15, config('venditio.routes.api.v1.pagination.per_page'));
        $this->assertTrue(config('venditio.routes.api.enable'));
        $this->assertFalse(config('venditio.routes.api.json_resource_enable_wrapping'));
        $this->assertTrue(config('venditio.slugs.regenerate_on_update'));
        $this->assertTrue(config('venditio.slugs.editable_via_api'));
        $this->assertSame([], config('venditio.slugs.resources'));
    }

    #[WithConfig('venditio', [
        'cart' => [
            'pipelines' => [
                'create' => [
                    'pipes' => [
                        CalculateTotals::class,
                    ],
                ],
            ],
        ],
    ])]
    public function test_published_host_list_config_remains_a_full_override(): void
    {
        $this->assertSame(
            [CalculateTotals::class],
            config('venditio.cart.pipelines.create.pipes')
        );

        $this->assertIsArray(config('venditio.cart.pipelines.update.pipes'));
        $this->assertNotEmpty(config('venditio.cart.pipelines.update.pipes'));
    }
}
