<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id('card_id');

            $table->string('card_number', 16);
            $table->string('card_expiration_date');
            $table->string('card_holder_name');
            $table->string('card_cvv');
            $table->string('card_first_digits', 6);
            $table->string('card_last_digits', 4);

            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');

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
        Schema::dropIfExists('cards');
    }
}
