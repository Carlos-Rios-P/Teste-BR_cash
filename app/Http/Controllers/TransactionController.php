<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function getStatus(Request $request)
    {
        $last_number_card = substr($request->card_number, -1);

        $status = null;

        if ($last_number_card <= 4 && $last_number_card != 0 ) {
            $status = Transaction::PAID;
        }

        if ($last_number_card >= 5 && $last_number_card != 9 ) {
            $status = Transaction::REFUSED;
        }

        if ($last_number_card == 9) {
            $status = rand(Transaction::PAID, Transaction::AUTHORIZED);
        }

        return $status;
    }

    public function store(Request $request)
    {
        if ($request->payment_method != 'credit_card')
        {
            return response()->json(['message' => 'No momento só aceitamos pagamentos via cartão de crédito']);
        }

        $first_six_number = substr($request->card_number, 0, 6);

        $last_four_number = substr($request->card_number, 12, 16);

        $transaction = Transaction::create([
            'installments'      => $request->installments ?? 1,
            'amount'            => $request->amount,
            'captured_amount'   => $request->amount,
            'paid_amount'       => $request->amount,
            'payment_method'    => $request->payment_method,
            'status'            => Transaction::PROCESSING,
            'ref_id'            => md5($request->id),
            'async'             => $request->async ?? true,
            'capture'           => $request->capture ?? true,
        ]);

        $card = Card::create([
            'card_number'           => $request->card_number,
            'card_expiration_date'  => $request->card_expiration_date,
            'card_holder_name'      => $request->card_holder_name,
            'card_cvv'              => $request->card_cvv,
            'transaction_id'        => $transaction->id,
            'card_first_digits'     => $first_six_number,
            'card_last_digits'      => $last_four_number,
        ]);

        $input = $this->getStatus($request);
        $transaction->update(['status' => $input]);

        if($input == 0){
            // $transaction->delete($request->all()); Se caso precisasse que o registro fosse excluído

            return response()->json(['message' => 'Não são aceitos cartões com o ultimo digito 0']);
        }

        if ($request->capture == false)
        {
            $transaction->update([
                'status'            => Transaction::AUTHORIZED,
                'captured_amount'   => null,
                'paid_amount'       => null,
            ]);
        }

        if ($transaction->async == true) {

            // CreateTransactionCardJob::dispatch(); //falta fazer

            return response()->json(['message' => 'Transação adcionada à fila!']);
        }

        //modelando o response
        $array = [
            'id' => $transaction->id,
            'installments' => $transaction->installments,
            'amount' => $transaction->amount,
            'captured_amount' => $transaction->captured_amount,
            'paid_amount' => $transaction->paid_amount,
            'payment_method' => $transaction->payment_method,
            'ref_id' => $transaction->ref_id,
            'status' => $transaction->status,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
            'card' => [
                'card_id' => $card->id,
                'card_holder_name' => $card->card_holder_name,
                'card_first_digits' => $card->card_first_digits,
                'card_last_digits' => $card->card_last_digits,
                'created_at' => $card->created_at,
                'update_at' => $card->updated_at,
            ]
        ];

        return response()->json($array, 200);
    }
}
