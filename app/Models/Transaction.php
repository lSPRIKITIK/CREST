<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $primaryKey = 'transaction_id';

    // Disables mass assignment protection
    protected $guarded = [];

    protected $casts = ['transaction_date' => 'date'];

    public function payment() {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
