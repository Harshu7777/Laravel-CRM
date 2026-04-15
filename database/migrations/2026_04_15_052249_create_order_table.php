<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // bigint unsigned AUTO_INCREMENT

            $table->unsignedBigInteger('user_id')->index();

            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 20)->nullable();

            $table->text('address');
            $table->string('city', 100);
            $table->string('zip_code', 20)->nullable();

            $table->decimal('total_amount', 10, 2);

            $table->decimal('exchange_rate_usd', 10, 4)->nullable();

            $table->enum('payment_status', [
                'pending_payment',
                'paid',
                'failed',
                'cancelled'
            ])->default('pending_payment');

            $table->enum('shipping_status', [
                'pending',
                'processing',
                'shipped',
                'delivered'
            ])->default('pending');

            $table->string('stripe_session_id')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            $table->dateTime('paid_at')->nullable();

            // Optional: Foreign key (recommended)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};