<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public const PROCESSING = 0;
    public const PAID       = 1;
    public const REFUSED    = 2;
    public const AUTHORIZED = 3;

    protected $fillable = [
        'installments',
        'amount',
        'captured_amount',
        'paid_amount',
        'ref_id',
        'status',
        'payment_method',
        'async',
        'capture',
    ];

    public function getAmountAttribute()
    {
        return $this->attributes['amount'] / 100;
    }

    public function getCapturedAmountAttribute()
    {
        return $this->attributes['captured_amount'] / 100;
    }

    public function getPaidAmountAttribute()
    {
        return $this->attributes['paid_amount'] / 100;
    }

    // public function setAmountAttribute($attr)
    // {
    //     return $this->attributes['amount'] = $attr * 100;
    // }

    public function card()
    {
        return $this->hasOne(Card::class);
    }
}
