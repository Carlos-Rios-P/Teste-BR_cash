<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_number',
        'card_expiration_date',
        'card_holder_name',
        'card_cvv',
        'transaction_id',
        'card_first_digits',
        'card_last_digits',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
