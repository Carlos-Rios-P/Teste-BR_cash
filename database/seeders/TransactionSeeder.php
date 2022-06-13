<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transaction::create([
            'installments' => 5,
            'amount' => 1000,
            'captured_amount' => 1000,
            'paid_amount' => 1000,
            'ref_id' => 'qualqueridUnico',
            'status' => 2,
            'payment_method' => 'credit_card',
            'async' => 0,
            'capture' => 1,
        ]);
    }
}
