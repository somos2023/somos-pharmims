<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Transaction::class, 'transaction_id');
            $table->foreignIdFor(\App\Models\Product::class, 'product_id');
            $table->double('selling_price');
            $table->integer('quantity')->default(1);
            $table->double('total');
            // $table->enum('discount_type', ['fix', 'percentage'])->default('fix');
            // $table->decimal('discount_amount', 10, 2)->default(0);
            // $table->double('net_total');
            $table->timestamp('created_at')->nullable(false)->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
