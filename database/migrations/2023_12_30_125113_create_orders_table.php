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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class, 'staff_id');
            $table->foreignIdFor(\App\Models\User::class, 'supplier_id');
            $table->string('order_number', 100)->unique();
            $table->integer('total_quantity')->default(1);
            $table->double('grand_total');
            $table->string('name', 100);
            $table->string('phone', 11);
            $table->longText('address');
            $table->longText('note')->nullable();
            $table->enum('status', ['to pay', 'to ship', 'to receive','received', 'cancelled'])->default('to pay');
            $table->boolean('deleted_flag')->default(0);
            $table->timestamp('created_at')->nullable(false)->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
