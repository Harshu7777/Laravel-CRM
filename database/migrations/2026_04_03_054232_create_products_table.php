<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name');                  // SEO friendly URL
            $table->string('sku')->unique();               
            $table->longText('description')->nullable();
            
            // Pricing
            $table->decimal('price', 12, 2);                     // Regular price
            $table->decimal('sale_price', 12, 2)->nullable();    // Discounted price
            $table->decimal('cost_price', 12, 2)->nullable();    // For profit calculation
            
            // Inventory
            $table->integer('stock_quantity')->default(0);
            
            // Category & Brand
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            
            // Media
            $table->string('image')->nullable();                 // Main product image
            $table->json('additional_images')->nullable();       // Multiple images
            
            // Product Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Soft deletes (optional but recommended)
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};