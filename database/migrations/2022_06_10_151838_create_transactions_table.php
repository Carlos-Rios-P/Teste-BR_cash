<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->integer('installments')->default(1);
            $table->integer('amount');
            $table->integer('captured_amount')->nullable();
            $table->integer('paid_amount')->nullable();
            $table->string('ref_id');
            $table->integer('status')->default(0);
            $table->string('payment_method');
            $table->boolean('async')->default(true);
            $table->boolean('capture')->default(true);


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
        Schema::dropIfExists('transactions');
    }
}
