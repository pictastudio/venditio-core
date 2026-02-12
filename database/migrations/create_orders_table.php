<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\Venditio\Models\{ShippingStatus, User};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->comment('utente può essere null se l\'app prevede la possibilità di acquistare senza registrazione');
            $table->foreignIdFor(ShippingStatus::class)->nullable();
            $table->string('identifier', 100)->unique();
            $table->string('status', 50)->index()->comment('pending, processing, completed, cancelled ecc...');

            // tracking
            $table->string('tracking_code', 150)->nullable();
            $table->string('tracking_link')->nullable();
            $table->dateTime('last_tracked_at')->nullable()->comment('data di aggiornamento del tracking');
            $table->string('courier_code', 50)->nullable();

            // totals
            $table->decimal('sub_total_taxable', 10, 2)->unsigned()->comment('somma dei totali dei prodotti senza tasse');
            $table->decimal('sub_total_tax', 10, 2)->unsigned()->comment('somma delle tasse dei prodotti');
            $table->decimal('sub_total', 10, 2)->unsigned()->comment('somma dei totali dei prodotti');
            $table->decimal('shipping_fee', 10, 2)->unsigned()->default(0)->comment('costo spese di spedizione');
            $table->decimal('payment_fee', 10, 2)->unsigned()->default(0)->comment('costo spese di pagamento');
            $table->string('discount_code')->nullable()->comment('codice sconto');
            $table->decimal('discount_amount', 10, 2)->unsigned()->default(0)->comment('valore assoluto dello sconto sull\'ordine');
            $table->decimal('total_final', 10, 2)->unsigned()->comment('totale finale ordine');

            // user
            $table->string('user_first_name')->nullable();
            $table->string('user_last_name')->nullable();
            $table->string('user_email')->nullable();

            // addresses
            $table->json('addresses')->comment('tutti i dati degli indirizzi salvati statici al momento dell\'ordine, formato: {"billing": {}, "shipping": {}}');

            // notes
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();

            // other
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
