<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount'                => 'required|integer|min:100',
            'payment_method'        => 'required',
            'installments'          => 'integer|between:1,12',
            'async'                 => 'boolean',
            'capture'               => 'boolean',

            'card_number'           => 'required_if:payment_method,credit_card|integer|digits:16|ends_with:1,2,3,4,5,6,7,8,9',
            'card_cvv'              => 'required_if:payment_method,credit_card|integer|digits:3',
            'card_expiration_date'  => 'required_if:payment_method,credit_card|digits:4',
            'card_holder_name'      => 'required_if:payment_method,credit_card|max:255',
        ];
    }
}
