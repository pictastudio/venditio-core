<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\VenditioCore\Packages\Simple\Models\{Order, User};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->comment('utente può essere null se l\'app prevede la possibilità di acquistare senza registrazione');
            $table->foreignIdFor(Order::class)->nullable();
            $table->string('identifier', 100)->unique();
            $table->string('status', 50)->index()->comment('active, converted, abandoned, cancelled');
            $table->decimal('sub_total_taxable', 10, 2, unsigned: true)->comment('somma dei totali dei prodotti senza tasse');
            $table->decimal('sub_total_tax', 10, 2, unsigned: true)->comment('somma delle tasse dei prodotti');
            $table->decimal('sub_total', 10, 2, unsigned: true)->comment('somma dei totali dei prodotti');
            $table->decimal('shipping_fee', 10, 2, unsigned: true)->default(0.00)->comment('costo spese di spedizione');
            $table->decimal('payment_fee', 10, 2, unsigned: true)->default(0.00)->comment('costo spese di pagamento');
            $table->string('discount_code', 30)->nullable()->comment('codice sconto');
            $table->decimal('discount_amount', 10, 2, unsigned: true)->default(0.00)->comment('valore assoluto dello sconto sul carrello');
            $table->decimal('total_final', 10, 2, unsigned: true)->comment('totale finale carrello');
            $table->string('user_first_name')->nullable();
            $table->string('user_last_name')->nullable();
            $table->string('user_email')->nullable();
            $table->json('addresses')->nullable()->comment('tutti i dati degli indirizzi salvati statici, formato: {"billing": {}, "shipping": {}}');
            $table->text('notes')->nullable();
            $table->datetimes();
            $table->softDeletesDatetime();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
