<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\VenditioCore\Packages\Simple\Models\ShippingStatus;
use PictaStudio\VenditioCore\Packages\Simple\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(ShippingStatus::class)->nullable();
            $table->string('identifier', 100)->unique();
            $table->string('status', 50)->index()->comment('pending, processing, completed, cancelled');
            $table->string('tracking_code')->nullable();
            $table->dateTime('tracking_date')->nullable()->comment('data di aggiornamento del tracking');
            $table->string('courier_code')->nullable();
            $table->decimal('sub_total_taxable', 10, 2)->comment('somma dei totali dei prodotti senza tasse');
            $table->decimal('sub_total_tax', 10, 2)->comment('somma delle tasse dei prodotti');
            $table->decimal('sub_total', 10, 2)->comment('somma dei totali dei prodotti');
            $table->decimal('shipping_fee', 10, 2)->default(0)->comment('costo spese di spedizione');
            $table->decimal('payment_fee', 10, 2)->default(0)->comment('tasse per spese di processo pagamento');
            $table->string('discount_code')->nullable()->comment('codice sconto');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('valore sconto sul carrello');
            $table->decimal('total_final', 10, 2)->comment('totale finale ordine');
            $table->string('user_first_name');
            $table->string('user_last_name');
            $table->string('user_email');
            $table->json('addresses')->comment('tutti i dati degli indirizzi salvati statici al momento dell\'ordine, formato: {"billing": {}, "shipping": {}}');
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->datetimes();
            $table->softDeletesDatetime();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
