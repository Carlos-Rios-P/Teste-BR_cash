<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Card;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Card::create([
            'card_id' => '1',
            'card_number' => '1234560000009876',
            'card_expiration_date' => '0728',
            'card_holder_name' => 'Cartão teste',
            'card_cvv' => '333',
            'card_first_digits' => '123456',
            'card_last_digits' => '9876',
            'transaction_id' => '1',
        ]);
    }
}
