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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Role::class, 'role_id');
            $table->foreignIdFor(\App\Models\User::class, 'user_id');
            $table->foreignIdFor(\App\Models\Category::class, 'category_id')->default(1);
            $table->string('barcode', 50);
            $table->string('brand_name', 100);
            $table->string('generic_name', 225)->nullable();
            $table->string('formulation', 50);
            $table->string('packing', 10);
            $table->date('expires_at');
            $table->double('price');
            $table->integer('stock')->default(0);
            $table->longText('description')->nullable();
            $table->enum('status', ['available', 'sold out'])->default('available');
            $table->string('image_url', 255)->nullable();
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
        Schema::dropIfExists('products');
    }
};
