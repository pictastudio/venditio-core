<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductType;
use PictaStudio\VenditioCore\Packages\Simple\Models\Brand;
use PictaStudio\VenditioCore\Packages\Simple\Models\TaxClass;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Brand::class);
            $table->foreignIdFor(ProductType::class);
            $table->foreignIdFor(TaxClass::class);
            $table->string('name');
            $table->string('status', 20)->index()->comment('draft, published, archived etc...');
            $table->boolean('active')->default(true);
            $table->boolean('new')->default(true);
            $table->boolean('in_evidence')->default(true);
            $table->dateTime('visible_from')->nullable()->index();
            $table->dateTime('visible_until')->nullable()->index();
            $table->text('description')->nullable();
            $table->text('description_short')->nullable();
            $table->json('images')->nullable();
            $table->json('files')->nullable();
            $table->string('measuring_unit', 50)->nullable()->comment('kg, g, l, ml, pz, etc...');
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('metadata')->nullable()->comment('metadati del prodotto (per SEO)');
            $table->datetimes();
            $table->softDeletesDatetime();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
