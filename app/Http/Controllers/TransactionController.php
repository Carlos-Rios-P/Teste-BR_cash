<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionCardRequest;
use App\Jobs\CreateCardJob;
use App\Models\Card;
use App\Models\Transaction;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function getStatus(TransactionCardRequest $request)
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

    public function store(TransactionCardRequest $request)
    {
        if ($request->payment_method != 'credit_card')
        {
            return response()->json(['Erro' => 'No momento só aceitamos pagamentos via cartão de crédito'], 406);
        }

        $first_six_number = substr($request->card_number, 0, 6);

        $last_four_number = substr($request->card_number, 12, 16);

        $transaction = Transaction::create([
            'installments'      => $request->installments ?? '1',
            'amount'            => $request->amount,
            'captured_amount'   => $request->amount,
            'paid_amount'       => $request->amount,
            'payment_method'    => $request->payment_method,
            'ref_id'            => md5($request->id),
            'status'            => Transaction::PROCESSING,
            'async'             => $request->async ?? true,
            'capture'           => $request->capture ?? true,
        ]);

        if ($transaction->async == true)
        {
            CreateCardJob::dispatch($request);

            return response()->json([$transaction,  ['message' => 'transação criada aguardando o cartão.']]);
        }

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

        if ($transaction->capture == false)
        {
            $transaction->update([
                'status'            => Transaction::AUTHORIZED,
                'captured_amount'   => null,
                'paid_amount'       => null,
            ]);

            $transaction->save();
        }

        $transaction->card;

        //modelando response
        unset($transaction['async'],
            $transaction['capture']);

        unset($transaction->card['card_number'],
            $transaction->card['card_cvv'],
            $transaction->card['card_expiration_date'],
            $transaction->card['transaction_id']);


         return response()->json($transaction, 200);
    }

    public function captureAmount($id, Request $request)
    {
        try {

            $data = Transaction::findOrFail($id);
            $data->card;

            $amountRequest = $request->amount / 100; //convertendo para reais

            if ( $amountRequest <= $data->amount && $data->status == Transaction::AUTHORIZED){

                $data->update([
                    'captured_amount' => $request->amount,
                    'paid_amount' => $request->amount,
                    'status' => Transaction::PAID,
                ]);

                $data->save();

                //modelando response
                unset($data['async'],
                    $data['capture']);;

                unset($data->card['card_number'],
                    $data->card['card_cvv'],
                    $data->card['card_expiration_date'],
                    $data->card['transaction_id']);

                return response()->json($data, 200);
            }

            return response()
                    ->json(["message' => 'O valor da quantia deve ser igual ou inferior a $data->amount e o status da transação deve ser Authorized"]);

        } catch (\Throwable $th) {

            return response()->json(['Erro' => 'ID da transação não encotrado'], 404);

        }
    }
}
