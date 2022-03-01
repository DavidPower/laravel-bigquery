<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('drip_id');
            $table->string('customer_name');
            $table->string('email');
            $table->string('event');
            $table->timestamp('event_datetime');
            $table->string('order_id');
            $table->string('product_id');
            $table->string('product_name');
            $table->decimal('amount_collected', 7, 2);
            $table->string('currency')->default('CAD');
            $table->string('payment_type')->default('manual');
            $table->boolean('reviewed')->default(false);
            $table->unsignedInteger('batch_id')->nullable();
            $table->timestamp('loaded_to_bigquery_datetime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_payments');
    }
}
