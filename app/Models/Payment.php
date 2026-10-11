<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';

    // Disables mass assignment protection
    protected $guarded = [];

    protected $casts = ['payment_date' => 'date'];

    public function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function transaction() {
        return $this->hasOne(Transaction::class, 'payment_id');
    }
}
