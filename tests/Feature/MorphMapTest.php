<?php

namespace PictaStudio\Venditio\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Orchestra\Testbench\Attributes\WithConfig;
use PictaStudio\Venditio\Models\Product;
use PictaStudio\Venditio\Tests\TestCase;
use SplFileInfo;

final class MorphMapTest extends TestCase
{
    public function test_every_package_model_is_configured_and_registered_with_its_stable_alias(): void
    {
        $configuredModels = config('venditio.models');

        $this->assertIsArray($configuredModels);

        foreach ($this->packageModelClasses() as $modelClass) {
            $alias = Str::snake(class_basename($modelClass));

            $this->assertArrayHasKey($alias, $configuredModels);
            $this->assertSame($modelClass, $configuredModels[$alias]);
            $this->assertSame($modelClass, Relation::getMorphedModel($alias));
            $this->assertSame($alias, (new $modelClass)->getMorphClass());
        }

        $this->assertEqualsCanonicalizing(
            $this->packageModelClasses(),
            array_values($configuredModels)
        );
    }

    #[WithConfig('venditio.models.product', MorphMapProductOverride::class, false)]
    public function test_a_host_model_override_replaces_the_package_model_in_both_morph_map_directions(): void
    {
        $this->assertSame(
            MorphMapProductOverride::class,
            Relation::getMorphedModel('product')
        );
        $this->assertSame('product', (new MorphMapProductOverride)->getMorphClass());
        $this->assertSame(Product::class, (new Product)->getMorphClass());
    }

    /**
     * @return list<class-string<Model>>
     */
    private function packageModelClasses(): array
    {
        return collect(glob(dirname(__DIR__, 2) . '/src/Models/*.php') ?: [])
            ->map(
                fn (string $path): string => 'PictaStudio\\Venditio\\Models\\'
                    . (new SplFileInfo($path))->getBasename('.php')
            )
            ->filter(fn (string $modelClass): bool => is_a($modelClass, Model::class, true))
            ->sort()
            ->values()
            ->all();
    }
}

final class MorphMapProductOverride extends Product
{
    protected $table = 'products';
}
