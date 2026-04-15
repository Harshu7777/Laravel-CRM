<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // bigint unsigned AUTO_INCREMENT

            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id');

            $table->string('product_name');

            $table->decimal('price', 10, 2);
            $table->integer('quantity');

            // Generated column (price * quantity)
            $table->decimal('total_price', 10, 2)
                  ->storedAs('price * quantity')
                  ->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Optional Foreign Keys (recommended)
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};